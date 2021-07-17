<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $question = $options['question'];

        if($question != null) {
            switch ( $question->getType() ) {
                case 'single' :
                    $builder->add('answer', ChoiceType::class, [
                        'label'=>false,
                        'choices' => $question->getVariants(),
                        'expanded' => true,
                        'multiple' => false,
                    ]);
                    $builder->get('answer')
                        ->addModelTransformer(new CallbackTransformer(
                            function ($answerArray) {
                                return count($answerArray)? $answerArray[0]: null;
                            },
                            function ($answerString) {
                                return [$answerString];
                            }
                        ));
                    break;

                case 'multiple' :
                    $builder->add('answer', ChoiceType::class, [
                        'label'=>false,
                        'choices' => $question->getVariants(),
                        'expanded' => true,
                        'multiple' => true,
                    ]);
                    break;

                case 'text' :
                    $builder->add('answer', TextType::class, [
                        'label'=>false,
                    ]);
                    $builder->get('answer')
                        ->addModelTransformer(new CallbackTransformer(
                            function ($answerArray) {
                                return count($answerArray)? $answerArray[0]: null;
                            },
                            function ($answerString) {
                                return [$answerString];
                            }
                        ));
                    break;
            }

        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
            'question' => null
        ]);
    }
}
