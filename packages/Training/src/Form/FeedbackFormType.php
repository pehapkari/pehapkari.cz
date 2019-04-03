<?php declare(strict_types=1);

namespace Pehapkari\Training\Form;

use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Entity\TrainingFeedback;
use Pehapkari\Training\Repository\TrainingRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @copy of https://docs.google.com/forms/d/1M3hK--ZqKzaJ2peO84N3KNX26dmcBzWjSTzvZ8JB4k4/edit?ts=5bc08baf
 */
final class FeedbackFormType extends AbstractType
{
    /**
     * @var string
     */
    private const ELEPHANT_EMOJI = '🐘';

    /**
     * @var TrainingRepository
     */
    private $trainingRepository;

    public function __construct(TrainingRepository $trainingRepository)
    {
        $this->trainingRepository = $trainingRepository;
    }

    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add('name', TextType::class, [
            'label' => 'Tvé jméno',
        ]);

        $formBuilder->add('training', EntityType::class, [
            'label' => 'Vyber školení',
            'class' => Training::class,
            'choices' => $this->trainingRepository->fetchRecentlyActive(),
        ]);

        $formBuilder->add('pointOfEntry', TextType::class, [
            'label' => 'Kde ses o kurzu dozvěděl?',
            'help' => 'Např. Facebook, kamarád, sraz...',
        ]);

        // @see https://symfony.com/doc/current/reference/forms/types/choice.html#select-tag-checkboxes-or-radio-buttons
        $formBuilder->add('ratingOrganization', ChoiceType::class, [
            'label' => 'Ohodnoť organizaci kurzu',
            'required' => true,
            'help' => '5 sloníků = nejlepší hodnocení',
            'choices' => $this->createRatingChoices(),
            'expanded' => true,
            'multiple' => false,
        ]);

        $formBuilder->add('ratingContent', ChoiceType::class, [
            'label' => 'Ohodnoť obsah kurzu',
            'required' => true,
            'help' => '5 sloníků = nejlepší hodnocení',
            'choices' => $this->createRatingChoices(),
            'expanded' => true,
            'multiple' => false,
        ]);

        $formBuilder->add('text', TextareaType::class, [
            'label' => 'Napiš nám svůj pocit ze školení, cokoliv tě napadne',
            'required' => true,
            'help' => 'Ideálně 1-3 věty :)',
        ]);

        $formBuilder->add('isAgreedWithPublishingName', CheckboxType::class, [
            'label' => 'Souhlasím se zveřejněním jména u své odpovědi na stránkách Péhápkařů',
        ]);

        $formBuilder->add('thingsToImprove', TextareaType::class, [
            'label' => 'Napadá tě něco, co můžeme zlepšit?',
            'required' => false,
        ]);

        $formBuilder->add('register', SubmitType::class, [
            'label' => 'Odeslat přihlášku',
            'attr' => [
                'class' => 'btn btn-success',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => TrainingFeedback::class,
        ]);
    }

    /**
     * @return float[]
     */
    private function createRatingChoices(): array
    {
        return [
            # label => value
            str_repeat(self::ELEPHANT_EMOJI, 1) => 1.0,
            str_repeat(self::ELEPHANT_EMOJI, 2) => 2.0,
            str_repeat(self::ELEPHANT_EMOJI, 3) => 3.0,
            str_repeat(self::ELEPHANT_EMOJI, 4) => 4.0,
            str_repeat(self::ELEPHANT_EMOJI, 5) => 5.0,
        ];
    }
}
