<?php

namespace App\Http\Controllers;

use App\Criteria\EmailCriteria;
use App\Criteria\TokenCriteria;
use App\Entities\ActivationToken;
use App\Http\Requests\ActivateUserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\SendActivationRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ValidateEmailRequest;
use App\Http\Requests\UserImportRequest;
use App\Entities\ResetPasswordToken;
use App\Repositories\ActivationTokenRepositoryEloquent;
use App\Repositories\ResetPasswordTokenRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use App\Services\UserService;
use Dingo\Api\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Entities\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends RestController
{
    use AuthenticatesUsers, RegistersUsers {
        AuthenticatesUsers::guard insteadof RegistersUsers;
        AuthenticatesUsers::redirectPath insteadof RegistersUsers;
    }

    /**
     * @var ActivationTokenRepositoryEloquent
     */
    protected $activationTokenRepository;

    /**
     * @var ResetPasswordTokenRepositoryEloquent
     */
    protected $resetPasswordTokenRepository;

    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * UserController constructor.
     * @param UserRepositoryEloquent $repository
     * @param UserService $userService
     * @param ActivationTokenRepositoryEloquent $activationTokenRepository
     * @param ResetPasswordTokenRepositoryEloquent $resetPasswordTokenRepository
     */
    public function __construct(
        UserRepositoryEloquent $repository,
        UserService $userService,
        NotificationService $notificationService,
        ActivationTokenRepositoryEloquent $activationTokenRepository,
        ResetPasswordTokenRepositoryEloquent $resetPasswordTokenRepository)
    {
        $this->repository = $repository;
        $this->activationTokenRepository = $activationTokenRepository;
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;

        $this->userService = $userService;
        $this->notificationService = $notificationService;
    }

    /**
     * @param RegisterUserRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function register(RegisterUserRequest $request)
    {
        $data = $request->only(['membership_id', 'email', 'username_forum', 'target_url']);
        $data['password'] = bcrypt($request->input('password'));
        $data['target_url'] = empty($data['target_url']) ? null : $data['target_url'];

        /**
         * Create the user
         */
        $user = $this->repository->create($data);

        /**
         * Create activation token for user
         *
         * @var ActivationToken $activationToken
         */
        $activationToken = $this->activationTokenRepository->createOrRefresh($user, $data['target_url']);

        /**
         * Send activation mail
         */
        $response = $this->notificationService->sendActivation($user, $activationToken->getToken());

        /**
         * Activate user
         */
        $this->repository->update(['status' => 'pending'], $user->id);

        return $this->successResponse('User created', 201);
    }

    /**
     * @param SendActivationRequest $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function sendActivation(SendActivationRequest $request)
    {
        /**
         * Fetch User
         *
         * @var User $user
         */
        $this->repository->pushCriteria(new EmailCriteria($request->email));

        $user = $this->repository->first();

        if($user !== null && ($user->status === 'pending'
                || $user->status === 'new')){

            /**
             * Refresh activation token for user
             *
             * @var ActivationToken $activationToken
             */
            $activationToken = $this->activationTokenRepository->createOrRefresh($user);

            /**
             * Send activation mail
             */
            $this->notificationService->sendActivation($user, $activationToken->getToken());
        }

        return $this->successResponse('Request processed', 200);
    }

    /**
     * @param ActivateUserRequest $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function activate(ActivateUserRequest $request)
    {
        $token = $request->input('token');

        /**
         * Fetch activationToken
         *
         * @var ActivationToken $activationToken
         */
        $this->activationTokenRepository->pushCriteria(new TokenCriteria($token));

        $activationToken = $this->activationTokenRepository->first();

        if ($activationToken === null || $activationToken->status !== 'available') {
            return $this->errorResponse('422 Unprocessable Entity', 422, ['token' => ['token.status']]);
        }

        /**
         * Activate user
         */
        $this->repository->update(['status' => 'active'], $activationToken->user->id);

        /**
         * Set Token to used
         */
        $this->activationTokenRepository->update(['status' => 'used'], $activationToken->id);

        /**
         * Generate (jwt) token again (but with claims)
         */
        $token = Auth::guard('api')->login($activationToken->user);

        $response = ['token' => $token];
        if(!is_null($activationToken->target_url)) {
            $response['target_url'] = $activationToken->target_url;
        }

        return $this->successResponse('Activation successful', 200, $response);
    }

    /**
     * @param LoginUserRequest $request
     * @return Response
     */
    public function login(LoginUserRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {

            /**
             * Validate and log in User
             */
            $token = Auth::guard('api')->attempt($credentials);

            /**
             * User validation
             */
            $user = Auth::guard('api')->getUser();

            if ($user === null){
                return $this->errorResponse('422 Unprocessable Entity', 422, ['email' => ['user.credentials']]);
            }

            if (!$user->isActive){
                return $this->errorResponse('422 Unprocessable Entity', 422, ['global' => ['user.status']]);
            }
        } catch (JWTException $e) {
            return $this->errorResponse('422 Unprocessable Entity', 422, ['global' => ['system.error']]);
        }

        return $this->successResponse('Authenticated', 200, ['token' => $token]);
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $email = $request->input('email');

        /**
         * Fetch User
         *
         * @var User $user
         */
        $this->repository->pushCriteria(new EmailCriteria($email));

        $user = $this->repository->first();

        /**
         * Create ResetPasswordToken for user
         *
         * @var ResetPasswordToken $resetPasswordToken
         */
        $resetPasswordToken = $this->resetPasswordTokenRepository->createOrRefresh($user);

        /**
         * Send password reset mail
         */
        $this->notificationService->sendResetPassword($user, $resetPasswordToken->getToken());

        return $this->successResponse('Reset link sent', 200);
    }

    /**
     * @param ResetPasswordRequest $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $token = $request->input('token');
        $newPassword = $request->input('password');

        /**
         * Fetch activationToken
         *
         * @var ActivationToken $activationToken
         */
        $this->resetPasswordTokenRepository->pushCriteria(new TokenCriteria($token));

        $resetPasswordToken = $this->resetPasswordTokenRepository->first();

        if ($resetPasswordToken === null || $resetPasswordToken->status !== 'available') {
            return $this->errorResponse('422 Unprocessable Entity', 422, ['token' => ['token.status']]);
        }

        /**
         * Update to new password
         */
        $this->repository->update(['password' => bcrypt($newPassword)], $resetPasswordToken->user->id);

        /**
         * Set Token to used
         */
        $this->resetPasswordTokenRepository->update(['status' => 'used'], $resetPasswordToken->id);

        return $this->successResponse('Password reset', 200);
    }

    /**
     * @param ChangePasswordRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $userId =  $request->input('user_id');

        $newPassword = $request->input('password_new');
        $oldPassword = $request->input('password_old');

        /**
         * @var User $user
         */
        try {
            $user = $this->repository->find($userId);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse('404 User not found', 404);
        }

        /**
         * Validate old password
         */
        if(!Auth::guard('api')->validate(['email' => $user->email, 'password' => $oldPassword])) {
            return $this->errorResponse('422 Unprocessable Entity', 422, ['password_old' => ['invalid']]);
        }

        /**
         * Update to new password
         */
        $this->repository->update(['password' => bcrypt($newPassword)], $userId);

        return $this->successResponse('200 OK', 200);

    }

    /**
     * @param ValidateEmailRequest $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function validateEmail(ValidateEmailRequest $request)
    {
        /**
         * Fetch User
         *
         * @var User $user
         */
        $this->repository->pushCriteria(new EmailCriteria($request->email));

        $user = $this->repository->first();

        /**
         * Handle responses
         */
        if($user === null)
        {
            return $this->successResponse('Email available', 200, ['email' => ['available']]);
        }

        if($user->status === 'pending'|| $user->status === 'new')
        {
            return $this->successResponse('User not active', 200, ['email' => ['user.status']]);
        }

        return $this->successResponse('User with email found', 200, ['email' => ['user.found']]);
    }
}
