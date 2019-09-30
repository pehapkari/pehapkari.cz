<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Social;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Marketing\Entity\MarketingEvent;
use Pehapkari\Marketing\Exception\TweetPublishFailedException;
use Pehapkari\Training\Entity\Trainer;
use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Entity\TrainingFeedback;
use Pehapkari\Training\Entity\TrainingTerm;
use TwitterAPIExchange;

final class TwitterPublisher
{
    /**
     * @var int
     */
    private const TWEET_LIMIT_SIZE = 280;

    /**
     * @var string
     */
    private const IMAGE_UPLOAD_URL = 'https://upload.twitter.com/' . self::API_VERSION . '/media/upload.json';

    /**
     * @var string
     */
    private const API_VERSION = '1.1';

    /**
     * @var string
     */
    private const UPDATE_URL = 'https://api.twitter.com/' . self::API_VERSION . '/statuses/update.json';

    /**
     * @var TwitterAPIExchange
     */
    private $twitterAPIExchange;

    /**
     * @var UrlFactory
     */
    private $urlFactory;

    public function __construct(TwitterAPIExchange $twitterAPIExchange, UrlFactory $urlFactory)
    {
        $this->twitterAPIExchange = $twitterAPIExchange;
        $this->urlFactory = $urlFactory;
    }

    public function publishMarketingEvent(MarketingEvent $marketingEvent): void
    {
        $trainingTerm = $marketingEvent->getTrainingTerm();
        if ($trainingTerm === null) {
            throw new ShouldNotHappenException();
        }

        $training = $trainingTerm->getTraining();
        $trainer = $trainingTerm->getTrainer();

        // make sure we have some references to tweet about
        $reference = $training->getFeedbacks()[0];
        if ($reference === null) {
            throw new ShouldNotHappenException(sprintf(
                'Complete some references for "%s" training first',
                $trainingTerm->getTrainingName()
            ));
        }

        $message = $this->createTwitterMessage($reference, $trainer, $trainingTerm, $training);

        if (Strings::length($message) >= self::TWEET_LIMIT_SIZE) {
            throw new TweetPublishFailedException('Tweet is too long: %d. Fit it under %d chars.', Strings::length(
                $message
            ), self::TWEET_LIMIT_SIZE);
        }

        $trainingImage = $training->getImageAbsolutePath();
        if ($trainingImage) {
            $response = $this->publishTweetWithImage($message, $trainingImage);
        } else {
            // tweet text only
            $response = $this->callPost(self::UPDATE_URL, [
                'status' => $message,
            ]);
        }

        // all good
        if (isset($response['created_at'])) {
            return;
        }

        throw new TweetPublishFailedException($response['errors'][0]['message']);
    }

    private function createTwitterMessage(
        TrainingFeedback $trainingFeedback,
        Trainer $trainer,
        TrainingTerm $trainingTerm,
        Training $training
    ): string {
        $message = '"' . $trainingFeedback . '"' . PHP_EOL . PHP_EOL;
        $message .= 'Přijď na školení od @' . $trainer->getTwitterName() . PHP_EOL;
        $message .= $this->urlFactory->createAbsoluteTrainingUrl($trainingTerm) . PHP_EOL;

        // @todo: už jen x dní do uzavření registrací

        // twitter hash tags
        if ($training->getHashtags()) {
            $message .= $training->getHashtags();
        }

        return $message;
    }

    /**
     * Ref: https://developer.twitter.com/en/docs/media/upload-media/api-reference/post-media-upload and
     * https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/post-statuses-update.html "media_ids"
     *
     * @return mixed[]
     */
    private function publishTweetWithImage(string $status, string $imageFile): array
    {
        $media = $this->callPost(self::IMAGE_UPLOAD_URL, [
            'media' => base64_encode(FileSystem::read($imageFile)),
        ]);

        return $this->callPost(self::UPDATE_URL, [
            'status' => $status,
            'media_ids' => $media['media_id'],
        ]);
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    private function callPost(string $endPoint, array $data): array
    {
        $jsonResponse = $this->twitterAPIExchange
            ->setPostfields($data)
            ->buildOauth($endPoint, 'POST')
            ->performRequest();

        return $this->decodeJson($jsonResponse);
    }

    /**
     * @return mixed[]
     */
    private function decodeJson(string $jsonResponse): array
    {
        return Json::decode($jsonResponse, Json::FORCE_ARRAY);
    }
}
