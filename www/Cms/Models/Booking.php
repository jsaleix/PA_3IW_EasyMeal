<?php

namespace CMS\Models;

use CMS\Models\Booking_settings;

class Booking extends CMSModels
{
    protected $id;
    protected $client;
    protected $date;
    protected $number;
    protected $status;

    public function setId($id){
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }

    public function setClient($client){
        $this->client = $client;
    }

    public function getClient(){
        return $this->client;
    }

    public function setDate($date){
        $this->date = $date;
    }

    public function getDate(){
        return $this->date;
    }

    public function setNumber($number){
        $this->number = $number;
    }

    public function getNumber(){
        return $this->number;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function getStatus(){
        return $this->status;
    }

    public function form($bookingSettings){
        return [
            "config"=>[
                "method"=>"POST",
                "action"=>"",
                "id"=>"form_content",
                "class"=>"form-content",
                "submit"=>"edit",
                "submitClass"=>"cta-blue width-80 last-sm-elem"
            ],
            "inputs"=>[
                "date"=>[
                    "type"=>"date",
                    "label"=>"heure de reservation",
                    "id"=>"date",
                    "class"=>"input-content",
                    "placeholder"=>"date",
                    "required"=>true,
                    "value"=>"2021-07-11"
                ],
                "time"=>[
                    "type"=>"time",
                    "label"=>"heure de reservation",
                    "id"=>"time",
                    "class"=>"input-content",
                    "placeholder"=>"time",
                    "required"=>true,
                    "value"=>"10:30"
                ],
                "number"=>[
                    "type"=>"number",
                    "label"=>"Nombre de personnes pour la reservation",
                    "id"=>"number",
                    "class"=>"input-content",
                    "placeholder"=>"10",
                    "required"=>true,
                    "max"=>$bookingSettings->getMaxNumberPerReservation(),
                    "value"=>5
                ]
            ]
        ];
    }
    
}