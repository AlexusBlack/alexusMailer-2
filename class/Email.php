<?php
class Email {
	public $to;
	public $from_name;
	public $from_email;
	public $reply_email;
	public $subject;
	public $additional;
	public $text;
	public $enumer;
	public $type;

	public function __construct($to, $from_name, $from_email, $reply_email, $subject, $additional, $text, $enumer, $type="text/html", $applyEnumer=true, $applyMacro=true)
    {
        $this->enumer = $enumer;
        $this->to = $to;

        $this->from_name = $from_name;
        $this->from_email = $from_email;
        
        $this->reply_email = trim($reply_email);
        
        $this->subject = $subject;

        $this->additional = $additional;
        $this->text = $text;        
        $this->type = $type;
        $this->personizeAdditional();
        if($applyEnumer) $this->processEnumer();
        if($applyMacro) $this->processMacro();
    }
    public function personizeAdditional() {
        if(strpos($this->to, ";")==false) return;

        $emailadds=explode(";", $this->to);
        foreach ($emailadds as $key => $value) {
            if($key==0)
                $this->to=$value;
            else
                $this->additional[$key-1]=$value;
        }
    }
    public function processEnumer() {
    	$this->from_name = $this->applyEnumer($this->from_name, $this->enumer);
        $this->from_email = $this->applyEnumer($this->from_email, $this->enumer);
        $this->reply_email = $this->applyEnumer($this->reply_email, $this->enumer);
        $this->subject = $this->applyEnumer($this->subject, $this->enumer);
    }
    public function processMacro() {
    	$this->to=Macro::File($this->to, $this->enumer);

    	$this->text=Macro::All($this->text, $this);
    	$this->from_name=Macro::All($this->from_name, $this);
    	$this->from_email=Macro::All($this->from_email, $this);
        $this->reply_email=Macro::All($this->reply_email, $this);
    	$this->subject=Macro::All($this->subject, $this);

    	foreach ($this->additional as $key => $value)
			$this->additional[$key]=Macro::All($this->additional[$key], $this);
    }
    //Заменяет список строк, на строку номер $enumer
    private function applyEnumer($field, $enumer) {
    	if(strpos($field, "\n")===false) return $field;
        $field=rtrim($field, "\n");
		$field_values=explode("\n", $field);
		$field_values_count=count($field_values);

		$value=$field_values[$enumer%$field_values_count];

		return  $value;
    }
}
?>