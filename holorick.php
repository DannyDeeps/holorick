<?php

require_once __DIR__ . '/inc/start.php';

use Discord\Discord;
use Discord\WebSockets\Event;
use Discord\Parts\Channel\Message;
use React\EventLoop\Loop;

use Holorick\Identify\Channels;
use Holorick\Cred\Cred;

$discord= new Discord([
    'token' => Cred::D_TOKEN,
    'loop' => Loop::get(),
    'disabledEvents' => [],
    'loadAllMembers' => true
]);

$discord->on('ready', function(Discord $discord)
{
    // Message Router
    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord)
    {
        if ($message->author->user->bot) return;

        switch ($message->channel_id)
        {
            case Channels::MOE_SCREENSHOTS:
                validateScreenshotMessage($message);
            break;
        }
    });
});

$discord->run();

function validateScreenshotMessage(Message $message)
{
    if (empty($message->attachments[0])) {
        $message->delete();
        $message->reply('Hey.. th.. _blugggh_.. that doesn\'t go there!');
    }
}