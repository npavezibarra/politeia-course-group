<?php
class PCG_ACF {
    public function __construct() {
        add_action('acf/init', [$this, 'register_fields']);
    }

    public function register_fields() {
        acf_add_local_field_group([
            'key' => 'group_pcg_program',
            'title' => 'Detalles del Programa Filosófico',
            'fields' => [
                [
                    'key' => 'field_related_groups',
                    'label' => 'Grupos Asociados',
                    'name' => 'related_groups',
                    'type' => 'relationship',
                    'post_type' => ['groups'],
                    'instructions' => 'Selecciona los grupos de cursos incluidos en este programa.',
                    'return_format' => 'id',
                ],
                [
                    'key' => 'field_program_price',
                    'label' => 'Precio del Programa',
                    'name' => 'program_price',
                    'type' => 'number',
                    'prepend' => '$',
                ],
                [
                    'key' => 'field_audio_intro',
                    'label' => 'Audio Introductorio',
                    'name' => 'audio_intro',
                    'type' => 'url',
                ],
                [
                    'key' => 'field_program_level',
                    'label' => 'Nivel del Programa',
                    'name' => 'program_level',
                    'type' => 'select',
                    'choices' => [
                        'beginner' => 'Principiante',
                        'intermediate' => 'Intermedio',
                        'advanced' => 'Avanzado'
                    ]
                ],
                [
                    'key' => 'field_cta_url',
                    'label' => 'Botón de Acción (URL)',
                    'name' => 'cta_url',
                    'type' => 'url',
                ]
            ],
            'location' => [[
                ['param' => 'post_type', 'operator' => '==', 'value' => 'course_program'],
            ]],
        ]);
    }
}
