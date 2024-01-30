<?php

namespace HoloRick;

use \Discord\Discord;
use \Discord\WebSockets\Event;
use \Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
// use \Discord\Parts\Embed\Embed;
// use \Discord\Parts\User\Member;
use \React\EventLoop\Loop;

use HoloRick\Message\Handler as MessageHandler;
// use HoloRick\Member\Handler as MemberHandler;

final class Bot {
  private Discord $discord;

  public function start() : void {
    $this->discord = new Discord([
      'token' => D_TOKEN,
      'loop' => Loop::get(),
      'disabledEvents' => [],
      'loadAllMembers' => true,
      'intents' => [
        Intents::GUILD_MEMBERS,
        Intents::GUILD_MESSAGES,
        Intents::GUILDS
      ]
    ]);

    $this->discord->on('ready', function (Discord $discord) {
      $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
        // if (!empty($message->author->user->bot)) return; // Bot Message
        // if (empty($message->channel_id)) return; // PM

        echo "Message Received\n";
        MessageHandler::incomingMessage($message, $discord);
      });

      // Are you a Rick or a Morty?
      // $this->discord->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
      //   MemberHandler::added();

      //   $question = new Embed($discord);
      //   $question
      //     ->setTitle('What up my Glip Glop!')
      //     ->setType(Embed::TYPE_RICH)
      //     ->setAuthor('Holo-Rick')
      //     ->setDescription("If you could solve all of life's mysteries, would you?")
      //     ->addField([
      //       'name' => 'Eh.. Whatever..',
      //       'value' => 1
      //     ])
      //     ->addField([
      //       'name' => 'Oh, that sounds like fun!',
      //       'value' => 2
      //     ]);

      //   $member->user->sendMessage("He.. Hey! This is a test to.. test.. if your testing... aw screw it, just answer the stupid question!", false, $question);
      // });
    });

    $this->discord->run();
  }
}



// function validateScreenshotMessage(Message $message) {
//   if (empty($message->attachments[0])) {
//     $message->delete();
//     $message->reply('Hey.. th.. _blugggh_.. that doesn\'t go there!');
//   }
// }

// function messageRouter(Message $message, Discord $discord) {
//   if (strstr($message->content, '!')) {
//     switch ($message->content) {
//       case '!testmsg':
//         $question = new Embed($discord);
//         $question
//           ->setTitle('What up my Glip Glop!')
//           ->setType(Embed::TYPE_RICH)
//           ->setAuthor('Holo-Rick')
//           ->setDescription("If you could solve all of life's mysteries, would you?")
//           ->addField([
//             'name' => 'Eh.. Whatever..',
//             'value' => 1
//           ])
//           ->addField([
//             'name' => 'Oh, that sounds like fun!',
//             'value' => 2
//           ]);

//         $message->author->user->sendMessage("He.. Hey! This is a test to.. test.. if your testing... aw screw it, just answer the stupid question!", false, $question);
//         break;
//     }
//   }
// }
