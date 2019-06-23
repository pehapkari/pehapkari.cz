<?php declare(strict_types=1);

namespace Pehapkari\Training\Form;

use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @copy of https://docs.google.com/forms/d/1M3hK--ZqKzaJ2peO84N3KNX26dmcBzWjSTzvZ8JB4k4/edit?ts=5bc08baf
 */
final class GenerateCertificateFormType extends AbstractType
{
    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    public function __construct(TrainingTermRepository $trainingTermRepository)
    {
        $this->trainingTermRepository = $trainingTermRepository;
    }

    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add('name', TextType::class, [
            'label' => 'Participant Name',
        ]);

        $formBuilder->add('training_term', EntityType::class, [
            'label' => 'Training Term',
            'class' => TrainingTerm::class,
            'choices' => $this->trainingTermRepository->getRecentlyActive(),
        ]);

        $formBuilder->add('submit', SubmitType::class, [
            'label' => 'Generate',
            'attr' => [
                'class' => 'btn btn-success btn-lg',
            ],
        ]);
    }
}
