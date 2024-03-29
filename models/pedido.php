<?php

class Pedido{
    private $id;
    private $usuario_id;
    private $provincia;
    private $localidad;
    private $direccion;
    private $coste;
    private $estado;
    private $fecha;
    private $hora;
    private $db;

    //Conexión a la bbdd
    public function __construct(){
        $this->db = Database::connect();
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId($id) : self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of usuario_id
     */
    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    /**
     * Set the value of usuario_id
     */
    public function setUsuarioId($usuario_id) : self
    {
        $this->usuario_id = $usuario_id;

        return $this;
    }

    /**
     * Get the value of provincia
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set the value of provincia
     */
    public function setProvincia($provincia) : self
    {
        $this->provincia = $this->db->real_escape_string($provincia);

        return $this;
    }

    /**
     * Get the value of localidad
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set the value of localidad
     */
    public function setLocalidad($localidad) : self
    {
        $this->localidad = $this->db->real_escape_string($localidad);

        return $this;
    }

    /**
     * Get the value of direccion
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set the value of direccion
     */
    public function setDireccion($direccion) : self
    {
        $this->direccion = $this->db->real_escape_string($direccion);

        return $this;
    }

    /**
     * Get the value of coste
     */
    public function getCoste()
    {
        return $this->coste;
    }

    /**
     * Set the value of coste
     */
    public function setCoste($coste) : self
    {
        $this->coste = $coste;

        return $this;
    }

    /**
     * Get the value of estado
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set the value of estado
     */
    public function setEstado($estado) : self
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get the value of fecha
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set the value of fecha
     */
    public function setFecha($fecha) : self
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get the value of hora
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set the value of hora
     */
    public function setHora($hora) : self
    {
        $this->hora = $hora;

        return $this;
    }
    
    public function getAll(){
        $productos = $this->db->query("SELECT * FROM pedidos ORDER BY id DESC");
        return $productos;
    }

    public function getOne(){
        $producto = $this->db->query("SELECT * FROM pedidos WHERE id = {$this->getId()}");
        return $producto->fetch_object();
    }

    public function getOneByUser(){
        $sql = "SELECT p.id, p.coste FROM pedidos p "
                //."INNER JOIN lineas_pedidos lp ON lp.pedido_id = p.id "
                ."WHERE p.usuario_id = {$this->getUsuarioId()} ORDER BY id DESC LIMIT 1";
            
        $pedido = $this->db->query($sql);
        return $pedido->fetch_object();
    }

    public function getAllByUser(){
        $sql = "SELECT p.* FROM pedidos p "
                //."INNER JOIN lineas_pedidos lp ON lp.pedido_id = p.id "
                ."WHERE p.usuario_id = {$this->getUsuarioId()} ORDER BY id DESC";
            
        $pedido = $this->db->query($sql);
        return $pedido;
    }

    public function getProductosByPedido($id){
        // $sql = "SELECT * FROM productos WHERE id IN 
        //         .(SELECT producto_id FROM lineas_pedidos WHERE pedido_id={$id})";

        $sql = "SELECT pr.*, lp.unidades FROM productos pr "
                ."INNER JOIN lineas_pedidos lp ON pr.id = lp.producto_id "
                ."WHERE lp.pedido_id={$id}";

        $productos = $this->db->query($sql);
        return $productos;     
    }

    public function save(){
        $sql = "INSERT INTO pedidos VALUES (null, {$this->getUsuarioId()}, '{$this->getProvincia()}', '{$this->getLocalidad()}', '{$this->getDireccion()}', {$this->getCoste()}, 'confirm', CURDATE(), CURTIME());";
        $save = $this->db->query($sql);

        $result = false;
        if($save){
            $result = true;
        }
        return $result;
    }

    public function save_linea(){
        $sql = "SELECT LAST_INSERT_ID() as 'pedido';";
        $query = $this->db->query($sql);
        $pedido_id = $query->fetch_object()->pedido;

        foreach($_SESSION['carrito'] as $elemento){
            $producto = $elemento['producto'];

            $insert = "INSERT INTO lineas_pedidos VALUES (NULL, {$pedido_id}, {$producto->id}, {$elemento['unidades']})";
            $save = $this->db->query($insert);
        }

        $result = false;
        if($save){
            $result = true;
        }
        return $result;
        
    }

    public function edit(){
        $sql = "UPDATE pedidos SET estado='{$this->getEstado()}' ";
        $sql .= " WHERE id={$this->getId()} ;";

        $save = $this->db->query($sql);

        $result = false;
        if($save){
            $result = true;
        }
        return $result;
    }
    

    
}

?>