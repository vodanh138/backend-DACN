<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RepositoryInterface;
use Closure;
use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var
     */
    protected mixed $model;

    /**
     * @throws Exception
     */
    public function __construct(protected Application $app)
    {
        $this->makeModel();
    }


    /**
     * Returns the current Model instance
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @throws Exception
     */
    public function resetModel(): void
    {
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model(): string;

    /**
     * @return Model
     * @throws Exception
     */
    public function makeModel(): Model
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param  int|null $limit
     * @param  array    $columns
     * @param  string   $method
     * @return mixed
     * @throws Exception
     */
    public function paginate(int $limit = null, array $columns = ['*'], string $method = "paginate"): mixed
    {
        $results = $this->model->{$method}($limit, $columns);
        $results->appends(app('request')->query());
        $this->resetModel();

        return $results;
    }

    /**
     * Save a new entity in repository
     *
     * @param  array $attributes
     * @return mixed
     * @throws Exception
     */
    public function create(array $attributes): mixed
    {
        $attributes = $this->model->newInstance()->forceFill($attributes)->
        makeVisible($this->model->getHidden())->toArray();
        $model = $this->model->newInstance($attributes);
        $model->save();
        $this->resetModel();

        return $model;
    }

    /**
     * Find data by id
     *
     * @param  mixed $id
     * @param  array $columns
     * @return mixed
     * @throws Exception
     */
    public function find(mixed $id, array $columns = ['*']): mixed
    {
        $model = $this->model->findOrFail($id, $columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Update a entity in repository by id
     *
     * @param  array $attributes
     * @param  mixed $id
     * @return mixed
     * @throws Exception
     */
    public function update(mixed $id, array $attributes): mixed
    {
        $model = $this->model->newInstance();
        $model->setRawAttributes([]);
        $model->setAppends([]);
        $attributes = $model->forceFill($attributes)->makeVisible($this->model->getHidden())->toArray();
        $model = $this->model->findOrFail($id);
        $model->fill($attributes);
        $model->save();
        $this->resetModel();

        return $model;
    }

    /**
     * Delete a entity in repository by id
     *
     * @param  mixed $id
     * @return int
     * @throws Exception
     */
    public function delete(mixed $id): int
    {
        $model = $this->find($id);
        $this->resetModel();

        return $model->delete();
    }

    /**
     * Find data by field and value
     *
     * @param  mixed      $field
     * @param  mixed|null $value
     * @param  array      $columns
     * @return mixed
     * @throws Exception
     */
    public function findByField(mixed $field, mixed $value = null, array $columns = ['*']): mixed
    {
        $model = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find data by multiple fields
     *
     * @param  array $where
     * @param  array $columns
     * @return mixed
     * @throws Exception
     */
    public function findWhere(array $where, array $columns = ['*']): mixed
    {
        $this->applyConditions($where);
        $model = $this->model->get($columns);
        $this->resetModel();

        return $model;
    }


    /**
     * Load relations
     *
     * @param  array|string $relations
     * @return $this
     */
    public function with(array|string $relations): static
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     *
     * @return void
     * @throws Exception
     */
    // phpcs:disable
    protected function applyConditions(array $where): void
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                //smooth input
                $condition = preg_replace('/\s\s+/', ' ', trim($condition));
                //split to get operator, syntax: "DATE >", "DATE =", "DAY <"
                $operator = explode(' ', $condition);
                if (count($operator) > 1) {
                    $condition = $operator[0];
                    $operator = $operator[1];
                } else {
                    $operator = null;
                }
                switch (strtoupper($condition)) {
                    case 'IN':
                        if (!is_array($val)) {
                            throw new Exception("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereIn($field, $val);
                        break;
                    case 'NOTIN':
                        if (!is_array($val)) {
                            throw new Exception("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereNotIn($field, $val);
                        break;
                    case 'DATE':
                        if (!$operator) {
                            $operator = '=';
                        }
                        $this->model = $this->model->whereDate($field, $operator, $val);
                        break;
                    case 'DAY':
                        if (!$operator) {
                            $operator = '=';
                        }
                        $this->model = $this->model->whereDay($field, $operator, $val);
                        break;
                    case 'MONTH':
                        if (!$operator) {
                            $operator = '=';
                        }
                        $this->model = $this->model->whereMonth($field, $operator, $val);
                        break;
                    case 'YEAR':
                        if (!$operator) {
                            $operator = '=';
                        }
                        $this->model = $this->model->whereYear($field, $operator, $val);
                        break;
                    case 'EXISTS':
                        if (!($val instanceof Closure)) {
                            throw new Exception("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereExists($val);
                        break;
                    case 'HAS':
                        if (!($val instanceof Closure)) {
                            throw new Exception("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereHas($field, $val);
                        break;
                    case 'HASMORPH':
                        if (!($val instanceof Closure)) {
                            throw new Exception("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereHasMorph($field, $val);
                        break;
                    case 'DOESNTHAVE':
                        if (!($val instanceof Closure)) {
                            throw new Exception("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereDoesntHave($field, $val);
                        break;
                    case 'DOESNTHAVEMORPH':
                        if (!($val instanceof Closure)) {
                            throw new Exception("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereDoesntHaveMorph($field, $val);
                        break;
                    case 'BETWEEN':
                        if (!is_array($val)) {
                            throw new Exception("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereBetween($field, $val);
                        break;
                    case 'BETWEENCOLUMNS':
                        if (!is_array($val)) {
                            throw new Exception("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereBetweenColumns($field, $val);
                        break;
                    case 'NOTBETWEEN':
                        if (!is_array($val)) {
                            throw new Exception("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereNotBetween($field, $val);
                        break;
                    case 'NOTBETWEENCOLUMNS':
                        if (!is_array($val)) {
                            throw new Exception("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereNotBetweenColumns($field, $val);
                        break;
                    case 'RAW':
                        $this->model = $this->model->whereRaw($val);
                        break;
                    default:
                        $this->model = $this->model->where($field, $condition, $val);
                }
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }
    // phpcs:enable

    /**
     * Trigger static method calls to the model
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments): mixed
    {
        return call_user_func_array([new static(), $method], $arguments);
    }

    /**
     * Trigger method calls to the model
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments): mixed
    {
        return call_user_func_array([$this->model, $method], $arguments);
    }
}
