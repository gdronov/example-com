<?php

use Gdronov\ExampleCom\ApiClient;
use Gdronov\ExampleCom\CommentHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    private MockObject $stubClient;

    protected function setUp() : void
    {
        $this->stubClient = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['request'])
            ->getMock();
    }

    protected function tearDown(): void
    {
        unset($this->stubClient);
    }

    public function testGetCommentList()
    {
        $count = rand(100, 10000);
        $offset = rand(0, 1000);
        $commentList = [
            (object) ['id' => rand(1,100), 'name' => 'Василий', 'text' => 'Первый комментарий'],
            (object) ['id' => rand(200,300), 'name' => 'Пётр', 'text' => 'Ещё один комментарий'],
        ];
        $returnObj = new stdClass();
        $returnObj->comments = $commentList;

        $this->stubClient->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(ApiClient::METHOD_GET),
                $this->equalTo('comments'),
                $this->callback(function($param) use ($count, $offset) {
                    return (
                        is_array($param) &&
                        $param['count'] === $count &&
                        $param['offset'] === $offset
                    );
                })
            )
            ->willReturn($returnObj);

        $handler = $this->getCommentHandler();
        $this->assertSame($commentList, $handler->getList($count, $offset));
    }

    public function testAddComment()
    {
        $newId = rand(100, 10000);
        $newName = 'Grigory';
        $newText = 'Some comment text';

        $returnObj = new stdClass();
        $returnObj->id = $newId;

        $this->stubClient->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(ApiClient::METHOD_POST),
                $this->equalTo('comment'),
                $this->callback(function($param) use ($newName, $newText) {
                    return (
                        is_array($param) &&
                        $param['name'] === $newName &&
                        $param['text'] === $newText
                    );
                })
            )
            ->willReturn($returnObj);

        $handler = $this->getCommentHandler();
        $this->assertSame($newId, $handler->add($newName, $newText));
    }

    public function testEditComment()
    {
        $commentId = rand(100, 10000);
        $newName = 'Alex';
        $newText = 'Changed text comment';

        $returnObj = new stdClass();
        $returnObj->id = $commentId;

        $this->stubClient->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(ApiClient::METHOD_PUT),
                $this->equalTo('comment/' . $commentId),
                $this->callback(function($param) use ($newName, $newText) {
                    return (
                        is_array($param) &&
                        $param['name'] === $newName &&
                        $param['text'] === $newText
                    );
                })
            )
            ->willReturn($returnObj);

        $handler = $this->getCommentHandler();
        $this->assertSame($commentId, $handler->edit($commentId, $newName, $newText));
    }

    private function getCommentHandler()
    {
        return new CommentHandler($this->stubClient);
    }
}
