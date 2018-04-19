<?php
namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use League\Fractal\TransformerAbstract;

/**
 * Class RestController
 * @package App\Http\Controllers
 */
abstract class RestController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers, SendsResponses;

    /**
     * @var RestRepository
     */
    protected $repository;

    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    /**
     * @param null $id
     * @return \Dingo\Api\Http\Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function get($id = null)
    {
        if(isset($id)){
            try {
                $record = $this->repository->find($id);
            } catch (ModelNotFoundException $exception)
            {
                return $this->errorResponse('404 Record not found', 404);
            }

            return $this->successResponse('200 OK', 200, [$this->repository->singleTerminology() => $record]);
        }

        /**
         * Apply query string filtering
         */
        $this->repository->pushCriteria(new FilterCriteria(request()->input()));

        /**
         * @var array $records
         */
        $records = $this->repository->all();

        return $this->successResponse('200 OK', 200, [$this->repository->pluralTerminology() => $records]);
    }

    /**
     * @param $id
     * @return \Dingo\Api\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id)
    {
        $input = request()->input();

        $data = array_filter($input, function($value) { return $value !== null; });

        try {
            $record = $this->repository->update($data, $id);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse('404 Record not found', 404);
        }

        return $this->successResponse('200 OK', 200, [$this->repository->singleTerminology() => $record]);
    }
}