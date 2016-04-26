<?php
  /**
  Formular-Anzeige.
  Basierend auf Klasse von Alex Homburg

  Jan. 06, Philipp Crocoll

  **/

    require_once('UIWeb1FormItem.class.php5');

    class CUIWeb1Form extends CUIWeb1Component
    {

        public $requiredExtraOption = '';

        protected $_arrItems = array();
        protected $_strFormName;
        protected $_showSubmit = true;
        protected $_showAction = true;




        public function __construct( $_strNewFormName, $_showSubmit=false, $_showAction=false )
        {
            $this->_strFormName = $_strNewFormName;
            $this->_showSubmit = $_showSubmit;
            $this->_showAction = $_showAction;
        }

        public function addItem( $name, $title, $value, $size=20, $type = 'text',
                            $edit=true, $requiredField=false )
        {
            $this->_arrItems[$name] = new FormItem( $name, $title, $value, $type,
                                            $size, $edit, $requiredField );
        }

        public function addItemObject( $_Item )
        {
            $this->_arrItems[$_Item->getName()] = $_Item;
        }

        /*
        public function setVisibleFields( $fields )
        {
            $this->_visibleFilds = $fields;
        }*/


        public function display()
        {
            // alle nicht ausgewählten 'hidden'-Felder anzeigen
            foreach( $this->_arrItems as $name => $Item )
            {
                if( $Item->getType() == 'hidden' )
                    $Item->displayHiddenItem();
            }
            if( $this->_showAction )
                print '<input type="hidden" name="Aktion" value="">';


            // alle ausgewählten Felder anzeigen
            foreach( $this->_arrItems as $name => $Item )
            {
               switch ($Item->getType())
               {

                 case 'dropdown':
                   $Item->displayDropdownItem();
                   break;
                 case 'text':
                 case 'password':
                   $Item->displayItem();
                   break;

               }

            }

            // Buttons anzeigen
            if( $this->_showSubmit )
            {
                print "<tr height=\"30\"><td valign=\"bottom\" colspan=\"2\" align=\"center\">";
                print "     <input type=\"reset\" class=\"Button\" name=\"reset\" value=\"Zurücksetzen\">&nbsp;&nbsp;&nbsp;";
                print "     <input type=\"submit\" class=\"Button\" name=\"submitChange\" value=\"    Speichern    \">";
                print "</td></tr>";
            }



        }


        public function getItemHandle( $key )
        {
            $reference =& $this->_arrItems[ $key ];
            return( $reference );
        }




}