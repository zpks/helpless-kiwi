<?php

namespace App\Form\Activity;

use App\Entity\Activity\Activity;
use App\Entity\Group\Group;
use App\Entity\Security\LocalAccount;
use App\Form\Location\LocationType;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActivityEditType extends AbstractType
{
    /** @var bool */
    protected $isAdmin = false;

    /** @var Group[] */
    protected $groups;

    public function __construct(TokenStorageInterface $tokenStorage, GroupRepository $groupRepo)
    {
        $user = !is_null($tokenStorage->getToken()) ? $tokenStorage->getToken()->getUser() : null;

        if ($user instanceof LocalAccount) {
            $this->isAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true);
            if ($this->isAdmin) {
                $this->groups = $groupRepo->findAll();
            } else {
                $this->groups = $groupRepo->findSubGroupsForPerson($user);
            }
        } else {
            $this->groups = [];
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Naam',
                'attr' => ['placeholder' => 'Leuke activiteit naam'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschrijving',
                'attr' => ['placeholder' => 'Beschrijf hier de activiteit'],
            ])
            ->add('location', LocationType::class, [
                'label' => 'Locatie',
                'help' => '  ',
            ]);
        if ($this->isAdmin) {
            $builder->add('author', EntityType::class, [
                'label' => 'Georganiseerd door',
                'class' => 'App\Entity\Group\Group',
                'required' => false,
                'placeholder' => 'Geen groep',
                'choices' => $this->groups,
                'choice_label' => function ($ref) {
                    return $ref->getName();
                },
                'help' => 'De groep die de activiteit organiseert.',
            ]);
        } elseif (1 != count($this->groups)) {
            $builder->add('author', EntityType::class, [
                'label' => 'Georganiseerd door',
                'class' => 'App\Entity\Group\Group',
                'required' => true,
                'choices' => $this->groups,
                'choice_label' => function ($ref) {
                    return $ref->getName();
                },
                'help' => 'De groep die de activiteit organiseert.',
            ]);
        }
        $builder->add('target', EntityType::class, [
            'label' => 'Activiteit voor',
            'class' => 'App\Entity\Group\Group',
            'required' => false,
            'placeholder' => 'Iedereen',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('t')
                    ->andWhere('t.register = TRUE');
            },
            'choice_label' => function ($ref) {
                return $ref->getName();
            },
            'help' => 'De activiteit kan exclusief voor een bepaalde groep worden georganiseerd.',
        ])
            ->add('visibleAfter', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label' => 'Zichtbaar vanaf',
                'help' => 'Wis datum/tijd om activiteit te verbergen voor altijd.',
                'required' => false,
            ])
            ->add('deadline', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label' => 'Deadline aanmelden',
                'help' => 'Dit is de datum/tijd waarna je niet meer kan aanmelden.',
                'required' => true,
            ])
            ->add('start', DateTimeType::class, [
                'label' => 'Activiteit begint om',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => true,
            ])
            ->add('end', DateTimeType::class, [
                'label' => 'Activiteit eindigt om',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'help' => '  ',
                'required' => true,
            ])
            ->add('capacity', IntegerType::class, [
                'label' => 'Capaciteit',
                'attr' => ['placeholder' => '20'],
                'help' => 'Het maximaal aantal aanmeldingen, hierna word je op de reserve lijst aangemeld.',
                'required' => false,
            ])
            ->add('color', ChoiceType::class, [
                'attr' => ['data-select' => 'true'],
                'choices' => [
                    '' => null,
                    'Rood' => 'red',
                    'Oranje' => 'orange',
                    'Geel' => 'yellow',
                    'Groen' => 'green',
                    'Cyaan' => 'cyan',
                    'Lichtblauw' => 'ltblue',
                    'Blauw' => 'blue',
                    'Paars' => 'purple',
                    'Roze' => 'pink',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
