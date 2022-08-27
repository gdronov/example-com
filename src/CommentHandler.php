<?php

namespace Gdronov\ExampleCom;

/**
 * Класс работы с комментариями
 * Class CommentHandler
 * @package Gdronov\ExampleCom
 */
class CommentHandler
{
    // ограничение количества возвращаемых комментариев в списке
    const DEFAULT_LIST_OFFSET = 0;
    const DEFAULT_LIST_COUNT = 100;

    private ApiClient $apiClient;

    /**
     * @param ApiClient $client
     */
    public function __construct(ApiClient $client)
    {
        $this->apiClient = $client;
    }

    /**
     * Получение списка комментариев: GET http://example.com/comments
     * Предполагаем, что можно управлять количеством комментариев
     * @param int $count
     * @param int $offset
     * @return array Список комментариев
     * @throws ApiException
     */
    public function getList(int $count = self::DEFAULT_LIST_COUNT, int $offset = self::DEFAULT_LIST_OFFSET): array
    {
        if ($count < 0) {
            $count = self::DEFAULT_LIST_COUNT;
        }
        if ($offset < 0) {
            $offset = self::DEFAULT_LIST_OFFSET;
        }
        $data = $this->apiClient->request(
            ApiClient::METHOD_GET,
            'comments',
            ['count' => $count, 'offset' => $offset]
        );

        return $data->comments;
    }

    /**
     * Создание нового комментария: POST http://example.com/comment
     * В случае успеха сервис возвращает числовой ID нового комментария
     * @param string $name
     * @param string $text
     * @return int
     * @throws ApiException
     */
    public function add(string $name, string $text): int
    {
        $result = $this->apiClient->request(
            ApiClient::METHOD_POST,
            'comment',
            ['name' => $name, 'text' => $text]
        );

        return intval($result->id);
    }

    /**
     * Изменение комментария: PUT http://example.com/comment/{id}
     * В случае успеха сервис возвращает числовой ID измененного комментария
     * @param int $id
     * @param string $name
     * @param string $text
     * @return int
     * @throws ApiException
     */
    public function edit(int $id, string $name, string $text): int
    {
        $result = $this->apiClient->request(
            ApiClient::METHOD_PUT,
            'comment/' . $id,
            ['name' => $name, 'text' => $text]
        );

        return intval($result->id);
    }
}
