<?php

namespace Slackiss\Bundle\VoicensBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MemberType extends AbstractType
{
    protected $isEdit;

    public function __construct($isEdit=false)
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
            ->add('username','text',[
                'label'=>'用户名(不能少于4个字符，不能多于36个字符，仅允许英文字母与数字)',
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('email','text',[
                'label'=>'电子信箱(必须使用已存在的合法的电子信箱)',
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('plainPassword',null,[
                'label'=>'密码(密码不能少于6个字符)',
                'required'=>!$this->isEdit,
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('roles','choice', array(
                'label'     => '用户角色(可以按住Ctrl键进行多选)',
                'required'  => true,
                'choices'   => array(
                    'ROLE_USER' => '会员',
                    'ROLE_AUTHOR'=>'作者',
                ),
                'preferred_choices'=>['ROLE_USER'],
                'multiple'  => true,
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'height:200px'
                ]
            ))
            ->add('submit','submit',[
                'label'=>'保存',
                'attr'=>[
                    'class'=>'btn btn-primary'
                ]
            ])
        ;
    }
    /**
     * @param OptionsResolverInterface $resolver
     *
      */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Slackiss\Bundle\VoicensBundle\Entity\Member'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'work_bundle_adminbundle_staff';
    }
}
