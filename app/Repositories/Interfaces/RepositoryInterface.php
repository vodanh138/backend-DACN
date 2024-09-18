<?php

namespace App\Repositories\Interfaces;

use Exception;

interface RepositoryInterface
{
    /**
     * Retrieve all data of repository, paginated
     *
     * @param  int|null $limit
     * @param  array    $columns
     * @param  string   $method
     * @return mixed
     * @throws Exception
     */
    public function paginate(int $limit = null, array $columns = ['*'], string $method = "paginate"): mixed;

    /**
     * Save a new entity in repository
     *
     * @param  array $attributes
     * @return mixed
     * @throws Exception
     */
    public function create(array $attributes): mixed;

    /**
     * Find data by id
     *
     * @param  mixed $id
     * @param  array $columns
     * @return mixed
     * @throws Exception
     */
    public function find(mixed $id, array $columns = ['*']): mixed;

    /**
     * Update a entity in repository by id
     *
     * @param  array $attributes
     * @param  mixed $id
     * @return mixed
     * @throws Exception
     */
    public function update(mixed $id, array $attributes): mixed;

    /**
     * Delete a entity in repository by id
     *
     * @param  mixed $id
     * @return int
     * @throws Exception
     */
    public function delete(mixed $id): int;

    /**
     * Find data by field and value
     *
     * @param  mixed      $field
     * @param  mixed|null $value
     * @param  array      $columns
     * @return mixed
     * @throws Exception
     */
    public function findByField(mixed $field, mixed $value = null, array $columns = ['*']): mixed;

    /**
     * Find data by multiple fields
     *
     * @param  array $where
     * @param  array $columns
     * @return mixed
     * @throws Exception
     */
    public function findWhere(array $where, array $columns = ['*']): mixed;

    /**
     * Load relations
     *
     * @param  array|string $relations
     * @return $this
     */
    public function with(array|string $relations): static;
}
