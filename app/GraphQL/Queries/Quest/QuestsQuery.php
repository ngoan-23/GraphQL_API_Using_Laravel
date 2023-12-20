<?php
namespace App\GraphQL\Queries\Quest;

use App\Models\Quest;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use App\GraphQL\Types\PaginatedQuestsType;

class QuestsQuery extends Query
{
    protected $attributes = [
        'name' => 'quests',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Quest'));
    }

    public function args(): array
    {
        return [
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'defaultValue' => 1,
            ],
            'perPage' => [
                'name' => 'perPage',
                'type' => Type::int(),
                'defaultValue' => 10,
            ],
            'sortBy' => [
                'name' => 'sortBy',
                'type' => Type::string(),
                'defaultValue' => 'id',
            ],
            'sortDesc' => [
                'name' => 'sortDesc',
                'type' => Type::boolean(),
                'defaultValue' => false,
            ],
            'search' => [
                'name' => 'search',
                'type' => Type::string(),
                'defaultValue' => null,
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $query = Quest::query();

        if (isset($args['sortBy']) && !empty($args['sortBy'])) {
            $orderDirection = $args['sortDesc'] ? 'desc' : 'asc';
            $query->orderBy($args['sortBy'], $orderDirection);
        }

        if (isset($args['perPage']) && !empty($args['perPage'])) {
            $perPage = $args['perPage'];
        }

        if (isset($args['page']) && !empty($args['page'])) {
            $page = $args['page'];
        }

        if (isset($args['search']) && !empty($args['search'])) {
            $query->where('title', 'LIKE', "%{$args['search']}%");
        }

        if (!empty($perPage) && !empty($page)) {
            $result = $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            $result = Quest::all();
        }
        return $result->items();
    }
}