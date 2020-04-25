<?php

namespace App\Form;

use App\Entity\Etablissement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtablissementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('price',ChoiceType::class,[
                'choices'=>$this->getChoices()
            ])
            ->add('lat')
            ->add('lng')
            ->add('open')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etablissement::class,
            'translation_domain'=>'forms',
        ]);
    }
    public function getChoices(){
        $choices = Etablissement::priceVal;
        $output = [];
        foreach ($choices as $k => $v){
            $output[$v] =$k;
        }
        return $output;
    }
}
