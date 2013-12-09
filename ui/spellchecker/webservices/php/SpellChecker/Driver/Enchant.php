<?php
/**
 * Spellchecker Enchant driver class
 * !! Enchant needs to be installed, as well as a back-end service (pspell, hspell etc) !!
 *
 * @package    jQuery Spellchecker (https://github.com/badsyntax/jquery-spellchecker)
 * @author     Richard Willis
 * @copyright  (c) Richard Willis
 * @license    https://github.com/badsyntax/jquery-spellchecker/blob/master/LICENSE-MIT
 */

namespace SpellChecker\Driver;

class Enchant extends \SpellChecker\Driver
{
	protected $_default_config = array(
		'lang' => 'en_GB'
	);

	public function __construct($config = array())
	{
		parent::__construct($config);

		if (!function_exists('enchant_broker_init'))
		{
			exit('Enchant library not found');
		}

		$this->broker = enchant_broker_init();

		
		
		$dictionary = realpath("./dictionaries/");
		
		
		

		
		enchant_broker_set_dict_path($this->broker, ENCHANT_MYSPELL, $dictionary);


		//$dicts = enchant_broker_list_dicts($this->broker);

	//	echo nl2br(print_r($dicts, true));
		//print_r($dicts);
		//exit();

		//$this->_config['lang'] = $this->_config['lang'].".UTF-8";
		//test_array($this->_config['lang']); 
		
		$this->dictionary = enchant_broker_request_dict($this->broker, $this->_config['lang']);
		$this->dictionary_custom = false;
		$custom = isset($_GET['custom'])?$_GET['custom']:"";
		if ($custom){
			$f3 = \Base::instance();
			$cfg = $f3->get("CFG");
			$custom = $cfg['upload']['folder'] . "/dictionaries/$custom";
			$custom = $f3->fixslashes($custom);
			//test_array($custom); 
			
			if (file_exists($custom)){
				$dictionary_custom = $custom;
				$this->dictionary_custom = enchant_broker_request_pwl_dict($this->broker, $dictionary_custom);
			}
		}
		
		
		
	

		if (!enchant_broker_dict_exists($this->broker, $this->_config['lang']))
		{
			exit('Enchant dictionary not found for lang: ' . $this->_config['lang']);
		}
	}

	public function get_word_suggestions($word = NULL)
	{
		$suggestions = array();
		if ($this->dictionary_custom){
			$r = enchant_dict_suggest($this->dictionary_custom, $word);
				if ($r){
					foreach ($r as $word){
						if (!in_array($word,$suggestions)) $suggestions[] = $word;
					}
				}
		}

		
			$r = enchant_dict_suggest($this->dictionary, $word);
			if ($r){
				foreach ($r as $word){
					if (!in_array($word,$suggestions)) $suggestions[] = $word;
				}
			}
		
		
		
		//test_array(array("word"=>$word,"sug"=>$suggestions)); 
		
		return $suggestions;
	}

	public function check_word($word = NULL)
	{

		//word = "Solly's";

		$return = $check_dic = enchant_dict_check($this->dictionary, $word);
		

		
		$check_dic_cus = true;
		if ($this->dictionary_custom){
			//$word = str_replace("'","\'",$word);
			$check_dic_cus = enchant_dict_check($this->dictionary_custom, $word);
			if ($check_dic_cus && $return == false) $return = true;
			
		}
		
		//if ($word=='Solly\'s'){
			//test_array(array("word"=>$word,"dic"=>$check_dic,"cus"=>$check_dic_cus,"ret"=>$return));
		//}
		
		return !$return;
	}
}