<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Testing\Controller;

use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;

/**
 * Class RestController
 *
 * @since 2.0
 *
 * @Controller(prefix="testRest")
 */
class RestController
{
    /**
     * Query
     * Uri:/user/

     * @RequestMapping(route="/testRestUser", method={RequestMethod::GET})
     *
     * @return array
     */
    public function list(): array
    {
        return ['list'];
    }


    /**
     * Create
     * Uri:/user
     *
     * @RequestMapping(route="user", method={RequestMethod::POST,RequestMethod::PUT})
     *
     * @param Request $request
     *
     * @return array
     */
    public function create(Request $request): array
    {
        $name = $request->input('name');

        $bodyParams = $request->getParsedBody();
        $bodyParams = empty($bodyParams) ? ['create', $name] : $bodyParams;

        return $bodyParams;
    }

    /**
     * Query one
     * Uri:/user/6
     *
     * @RequestMapping(route="{uid}", method={RequestMethod::GET})
     *
     * @param int $uid
     *
     * @return array
     */
    public function getUser(int $uid): array
    {
        return ['getUser', $uid];
    }

    /**
     * Query user book
     * Uri:/user/6/book/8
     *
     * @RequestMapping(route="{userId}/book/{bookId}", method={RequestMethod::GET})
     *
     * @param int    $userId
     * @param string $bookId
     *
     * @return array
     */
    public function getBookFromUser(int $userId, string $bookId): array
    {
        return ['bookFromUser', $userId, $bookId];
    }

    /**
     * Delete
     * Uri:/user/6
     *
     * @RequestMapping(route="{uid}", method={RequestMethod::DELETE})
     *
     * @param int $uid
     *
     * @return array
     */
    public function deleteUser(int $uid): array
    {
        return ['delete', $uid];
    }

    /**
     * Update
     * Uri:/user/6
     *
     * @RequestMapping(route="{uid}", method={RequestMethod::PUT, RequestMethod::PATCH})
     *
     * @param int     $uid
     * @param Request $request
     *
     * @return array
     */
    public function updateUser(Request $request, int $uid): array
    {
        $body           = $request->getParsedBody();
        $body['update'] = 'update';
        $body['uid']    = $uid;

        return $body;
    }
}