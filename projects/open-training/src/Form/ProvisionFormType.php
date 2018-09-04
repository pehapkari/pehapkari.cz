<?php declare(strict_types=1);

namespace App\Form;

use App\Request\ProvisionFormRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProvisionFormType extends AbstractType
{
    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add('incomeAmount', IntegerType::class, [
            'data' => 0,
        ]);

        $formBuilder->add('lectorExpenses', IntegerType::class, [
            'label' => 'Lector',
            'required' => false,
            'help' => 'E.g. Lunch',
            'data' => 0,
        ]);

        $formBuilder->add('organizerExpenses', IntegerType::class, [
            'label' => 'Organizer',
            'required' => false,
            'help' => 'E.g. Prints, Certificates',
            'data' => 0,
        ]);

        $formBuilder->add('ownerExpenses', IntegerType::class, [
            'label' => 'Owner',
            'required' => false,
            'help' => 'E.g. Rent',
            'data' => 0,
        ]);

        $formBuilder->add('submit', SubmitType::class, [
            'label' => 'Compute Provisions',
        ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => ProvisionFormRequest::class,
        ]);
    }
}
