<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Social;

use Facebook\Facebook;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Marketing\Entity\MarketingEvent;
use Pehapkari\Marketing\Exception\TweetPublishFailedException;
use Pehapkari\Marketing\Utils\DateTimeUtils;
use Pehapkari\Training\Entity\Trainer;
use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Entity\TrainingFeedback;
use Pehapkari\Training\Entity\TrainingTerm;

final class FacebookPublisher
{
    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var Facebook
     */
    private $facebook;

    public function __construct(UrlFactory $urlFactory, Facebook $facebook)
    {
        $this->urlFactory = $urlFactory;
        $this->facebook = $facebook;
    }

    public function publishMarketingEvent(MarketingEvent $marketingEvent): void
    {
        $trainingTerm = $marketingEvent->getMarketingCampaign()->getTrainingTerm();
        if ($trainingTerm === null) {
            throw new ShouldNotHappenException();
        }

        $training = $trainingTerm->getTraining();
        $trainer = $trainingTerm->getTrainer();

        // make sure we have some references to tweet about
        $reference = $training->getReferences()[0];
        if ($reference === null) {
            throw new ShouldNotHappenException(sprintf(
                'Complete some references for "%s" training first',
                $trainingTerm->getTrainingName()
            ));
        }

        $message = $this->createMessage($reference, $trainer, $trainingTerm, $training);

        $trainingTermImage = $trainingTerm->getTrainingTermImageAbsolutePath();
        if ($trainingTermImage) {
//            $response = $this->publishTweetWithImage($message, $trainingTermImage);
        }
        // tweet text only
//            $response = $this->callPost(self::UPDATE_URL, [
//                'status' => $message,
//            ]);

        $appScopedUsedId = '685012615261982';
        $facebookResponse = $this->facebook->post(sprintf('https://graph.facebook.com/%s/feed
  ?message=Hello Fans!', $appScopedUsedId));

        // all good
        if (isset($response['created_at'])) {
            return;
        }

        throw new TweetPublishFailedException($response['errors'][0]['message']);
    }

    private function createMessage(
        TrainingFeedback $trainingFeedback,
        Trainer $trainer,
        TrainingTerm $trainingTerm,
        Training $training
    ): string {
        $message = '"' . $trainingFeedback . '"' . PHP_EOL . PHP_EOL;
        $message .= 'Přijď na školení od ' . $trainer->getName() . PHP_EOL;
        $message .= $this->urlFactory->createAbsoluteTrainingUrl($trainingTerm) . PHP_EOL;

        $daysTillDeadline = DateTimeUtils::getDayDifferenceFromNow($trainingTerm->getDeadlineDateTime());

        if ($daysTillDeadline < 15 && $daysTillDeadline > 1) {
        }

        // @todo: už jen x dní do uzavření registrací

        // twitter hash tags
        if ($training->getHashtags()) {
            $message .= $training->getHashtags();
        }

        return $message;
    }
}
