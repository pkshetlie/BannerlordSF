<?php

namespace App\Service;


use App\Entity\Participation;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DiscordService
{

    private UrlGeneratorInterface $url_generator;
    private $webhookUrl;

    public function __construct(string $webhookUrl, UrlGeneratorInterface $urlGenerator)
    {
        $this->url_generator = $urlGenerator;
        $this->webhookUrl = $webhookUrl;

    }

    public function sendValidatedParticipation(Participation $participation)
    {
        $timestamp = date("c", strtotime("now"));

        $json_data = json_encode([
            // Message
//            "content" => "Hi vikings, there is a new map",

            // Username
            "username" => "HC Bot",
            "avatar_url"=> $this->url_generator->generate('home',[],UrlGeneratorInterface::ABSOLUTE_URL)."flags/hc-bot.png",

            // Text-to-speech
            "tts" => false,

            // File upload
//            "file" => "",

            // Embeds Array
            "embeds" => [
                [
                    // Embed Title
                    "title" => "Nouvelle participation validée !",

                    // Embed Type
                    "type" => "rich",

                    // Embed Description
//                    "description" => "",

                    // URL of title link
                    "url" => $this->url_generator->generate('challenge_participer',['id'=>$participation->getChallenge()->getId()],UrlGeneratorInterface::ABSOLUTE_URL) ,
                    // Timestamp of embed must be formatted as ISO8601
//                    "timestamp" => $timestamp,

                    // Embed left border color in HEX
//                    "color" => "'".hexdec("3366ff")."'",

                    // Footer
                    "footer" => [
//                        "text" => "GitHub.com/Mo45",
                        "text" => $participation->getUser()->getTwitchID() != null ? "https://twitch.tv/" . $participation->getUser()->getTwitchID() :"Chaine twitch inconnue",
                        //"icon_url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=375"
                    ],

                    // Image to send
                    "image" => [
//                        $this->url_generator->generate("home", [], UrlGeneratorInterface::ABSOLUTE_URL)
                        "url" => $this->url_generator->generate('home',[],UrlGeneratorInterface::ABSOLUTE_URL)."uploads/challenge/banner/".$participation->getChallenge()->getValidationParticipationImage()
                    ],

                    // Thumbnail
                    //"thumbnail" => [
                    //    "url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=400"
                    //],

                    // Author
//                    "author" => [
//                        "name" => "krasin.space",
//                        "url" => "https://krasin.space/"
//                    ],

                    // Additional Fields array
                    "fields" => [
////                        // Field 1
                        [
                            "name" => "Joueur",
                            "value" => "'" . $participation->getUser()->getUsername() . "'",
                            "inline" => true
                        ],[
                            "name" => "Challenge",
                            "value" => "" . $participation->getChallenge()->getTitle() . "",
                            "inline" => true
                        ],
//                        [
//                            "name" => "Twitch",
//                            "value" => $participation->getUser()->getTwitchID() != null ? "https://twitch.tv/" . $participation->getUser()->getTwitchID() :"'Non renseigné'",
//                            "inline" => true
//                        ]
////                        // Etc..
                    ]
                ]
            ]

        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init($this->webhookUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resp = curl_exec($ch);
        curl_close($ch);
    }
}

