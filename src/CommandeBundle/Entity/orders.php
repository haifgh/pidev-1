<?php


namespace CommandeBundle\Entity;




class orders
{
    private $data;
    private $tel;
    private $address;

    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**w
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }






}