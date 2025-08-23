<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\TypeEnum;
use InvalidArgumentException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function sprintf;

/**
 * @extends AbstractType<EntryData>
 */
final class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $type = $options['type'];
        $builder->add('title', TextType::class);
        match ($type) {
            TypeEnum::TEXT => $builder->add('text', TextareaType::class),
            TypeEnum::FILE => $builder->add('file', FileType::class),
            TypeEnum::LINK => $builder->add('url', UrlType::class),
            default => throw new InvalidArgumentException(sprintf('Unsupported type: %s', $type->value)),
        };
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntryData::class,
            'validation_groups' => function (FormInterface $form): array {
                /** @var TypeEnum $type */
                $type = $form->getConfig()
                    ->getOption('type');

                return match ($type) {
                    TypeEnum::TEXT => ['Default', 'text'],
                    TypeEnum::FILE => ['Default', 'file'],
                    TypeEnum::LINK => ['Default', 'link'],
                };
            },
        ]);
        $resolver->setRequired('type');
        $resolver->setAllowedTypes('type', TypeEnum::class);
    }
}
