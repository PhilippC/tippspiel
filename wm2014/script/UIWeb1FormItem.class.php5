<?php
/**
 Formular-Item-Klasse.
 Basierend auf Klasse von Alex Homburg.

 Jan. 06, Philipp Crocoll

 **/

class FormItem extends CUIWeb1Component
{
	protected $_name          = '';
	protected $_title         = '';
	protected $_value         = '';
	protected $_size          = 20;
	protected $_edit          = true;
	protected $_type          = 'text';
	protected $td_class       = "class=\"Feld\"";
	protected $textAreaWidth  = 50;
	protected $selected       = 0;


	// contructor
	public function __construct( $name, $title, $value, $type='text', $size=20,
	$edit = true)
	{
		$this->_name    = $name;
		$this->_title   = $title;
		$this->_value   = $value;
		$this->_type    = $type;
		$this->_size    = $size;
		$this->_edit    = $edit;
	}


	public function displayItem()
	{
		// Feld zum schreiben?
		$strEdit = '';
		if( !$this->_edit )
		$strEdit = 'readonly';

		// Feld Titel
		print '<span class="FormItemTitle">' .$this->_title. ': ' .chr(10);

		// Kleine Felder normal setzen
		if( $this->_size < 100 )
		{
			print '<span class="FormItemSmall"><input name="' .$this->_name
			.'" type="'.$this->_type.'" value="' .htmlentities( $this->_value, ENT_QUOTES ) .'"
                    size="' .$this->_size .'" ' .$this->td_class .' ' .$strEdit .' /></span>' .chr(10) ;
			print '<div style="clear:both"></div>';
		}
		// Grosse Felder in textareas setzen
		else{
			$numRows = round( $this->_size / $this->textAreaWidth );
			print '<textarea name="' .$this->_name .'" rows="' .$numRows
			.'"cols="' .$this->textAreaWidth .'" class="Feld">'
			.htmlentities( $this->_value ) .'</textarea>' .chr(10) ;
		}
	}


	public function displayDropdownItem()
	{

		// Feld Titel
		print '<tr>' .chr(10);
		print '<td valign="top">' .$this->_title. ': </td>' .chr(10);

		// DD anzeigen
		print '<td><select '.$this->td_class.' name="' .$this->_name .'" >
                  ';
		if( $this->selected == 0 )
		print '<option selected="selected" value="0">--Bitte wählen--</option>
                      ';

		foreach( $this->_value as $key => $value ){
			print '<option ';
			if( (string)$key == (string)$this->selected )
			print 'selected="selected" ';
			print 'value="' .$key .'">' .$value .'</option>
                ';
		}
		print "</select>
            </td>";
		print "</tr>";
	}

	public function setSelected( $_id )
	{
		$this->selected = $_id;
	}

	public function displayHiddenItem()
	{
		print "<input type=\"hidden\"name=\"$this->_name\" value=\"$this->_value\" />";
	}


	public function getType()
	{
		return $this->_type;
	}


	public function getTitle()
	{
		return( $this->_title );
	}
	public function getName()
	{
		return $this->_name;
	}



	public function setSize( $size )
	{
		$this->_size = $size;
	}

	public function setTitle( $var )
	{
		$this->_title = $var;
	}

	public function setEdit( $var )
	{
		$this->_edit = $var;
	}

	public function setType( $var )
	{
		$this->_type = $var;
	}

	public function setClass($className)
	{
		if($className=="")
		$this->td_class = "";
		else
		$this->td_class = 'class="'.$className.'"';
	}




}