<?php

// config for HusamTariq/FilamentCertificateGenerator
return [

    /* certificate date */
    "certificate-data"=>[
        "course-name"=>"Course Name",
        "student-name"=>"Student Name",
        "trainer-name"=>"Trainer Name",
        "issue-date"=>"Issue Date",
        "hours"=>"Hours",
        "signature"=>"Signature",
    ],

    'types' => [
        'qualification' => [
            'label' => 'Qualification Certification',
            'description' => 'Formal attestation of completed academic requirements conferring a degree, diploma, or formal qualification',
            'color' => 'emerald',
            'icon' => 'heroicon-o-academic-cap',
        ],
        'participation' => [
            'label' => 'Participation Attestation',
            'description' => 'Verification of attendance and engagement in learning activities without formal assessment',
            'color' => 'sky',
            'icon' => 'heroicon-o-user-group',
        ],
    ],


    /* enable qrcode */
    "enable-qrcode"=>true,

];
