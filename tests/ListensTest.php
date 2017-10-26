<?php

class RespondsTest extends TestCase
{
    use \App\Traits\Listens;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testOKWebhookObject()
    {
        $object = [
          'object'=> 'page',
          'entry' => [
            [
              'id'       => 'PAGE_ID',
              'time'     => 1458692752478,
              'messaging'=> [
                [
                  'sender'=> [
                    'id'=> 4425522242,
                  ],
                  'recipient'=> [
                    'id'=> '...',
                  ],
                  'timestamp'=> 1458692752478,
                  'message'  => [
                    'mid'        => 'mid.1457764197618=>41d102a3e1ae206a38',
                    'text'       => 'Is 3A12 available tomorrow at 10?',
                    'quick_reply'=> [
                      'payload'=> null,
                    ],
                  ],
                ],
              ],
            ],
          ],
        ];

        $result = $this->parse($object);

        $this->assertEquals(4425522242, $result->senderId);
        $this->assertEquals('mid.1457764197618=>41d102a3e1ae206a38', $result->messageId);
        $this->assertEquals('Is 3A12 available tomorrow at 10?', $result->messageText);
    }

    public function testBadObject()
    {
        $this->expectException(\App\Exceptions\BadArgumentsException::class);

        $object = [
          'object'=> 'page',
          'entry' => [
            [
              'id'  => 'PAGE_ID',
              'time'=> 1458692752478,
            ],
          ],
        ];

        $result = $this->parse($object);
    }

    public function testBadSubscribe()
    {
        $this->get('/');
        $this->assertResponseStatus(401);
    }

    public function testGoodSubscribeAuth()
    {
        $this->get('/?'.http_build_query([
            'hub_verify_token' => 'messenger-key',
            'hub_mode'         => 'subscribe',
            'hub_challenge'    => 42,
        ]));

        $this->assertEquals(42, $this->response->getContent());
    }

    public function testBadSubscribeMode()
    {
        $this->get('/?'.http_build_query([
            'hub_verify_token' => 'messenger-key',
            'hub_mode'         => 'something',
            'hub_challenge'    => 42,
        ]));

        $this->assertResponseStatus(422);
    }
}
