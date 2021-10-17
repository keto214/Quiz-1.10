<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element;
use Laminas\Form\Form;

class UploadForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('upload_avatar');
        $this->setAttributes([
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ]);

        #add the file input field
        $this->add([
            'type' => Element\File::class,
            'name' => 'photo',
            'options' => [
                'label' => 'Choose A File',
                'label_attributes' => [
                    'class' => 'custom-file-label',
                ]
            ],
            'attributes' => [
                'required' => true,
                'class' => 'custom-file-input',
                'id' => 'photo',
                'multiple' => false, # not necessary. just added to show that you can allow multiple y setting this to true

            ]
        ]);

        # add the submit button
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'upload_photo',
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn btn-secondary'
            ]
        ]);
    }
}
