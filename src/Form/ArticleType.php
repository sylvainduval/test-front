<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title', TextType::class, [
				'label' => 'Titre',
				'constraints' => [
					new NotBlank(),
					new Length(['min' => 5, 'minMessage' => 'Le titre doit contenir au moins 5 caractères.']),
				],
			])
			->add('leading', TextareaType::class, [
				'label' => 'Accroche',
			])
			->add('body', TextAreaType::class, [
				'label' => 'Corps',
			])
			->add('createdBy', TextType::class, [
				'label' => 'Votre nom',
				'constraints' => [
					new NotBlank(),
					new Length(['min' => 2, 'minMessage' => 'Votre nom doit contenir au moins 2 caractères.']),
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			// Configure your form options here
		]);
	}
}
