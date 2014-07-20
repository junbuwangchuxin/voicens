<?php

namespace Slackiss\Bundle\VoicensBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{

    protected $isEdit;

    public function __construct($isEdit = false)
    {
        $this->isEdit = $isEdit;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'label' => '标题',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('attach', 'file', array(
                'label' => '封面图',
                'required' => !$this->isEdit,
                'attr' => array()
            ))
            ->add('summary', 'textarea', [
                'label' => '摘要',
                'required' => false,
                'attr' => [
                    'rows' => 8,
                    'class' => 'form-control'
                ]
            ])
            ->add('content', 'ckeditor', array(
                'label' => '内容',
                'attr' => array(
                    'class' => 'form-control'
                ),
                'filebrowser_image_browse_url' => array(
                    'route' => 'elfinder',
                    'route_parameters' => array(),
                ),
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Slackiss\Bundle\VoicensBundle\Entity\Article'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'slackiss_bundle_voicensbundle_article';
    }
}
