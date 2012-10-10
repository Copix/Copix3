<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     errormsg
 * Version:  1.0
 * Date:     08/05/2003
 * Author:	 gérald croes <gerald@phpside.org>
 * Purpose:  automated error message.
 *
 * Input:    message = (required if you want to display a message) the error message
 *           class = (optional) class css to use for the paragraph
 *           assign = (optional) text to display, default is address
 *
 * Examples: {errormsg message="Please give an adress"}
 *           {errormsg message="Please give an adress" class="redText"}
 *           {errormsg message=$Message assign=$errorMessage}
 */
function smarty_function_errormsg($params, &$this) {
   extract($params);

   if ($message === null || strlen (trim ($message)) == 0){
      //if message isNull or empty, nothing to do.
      $output = '';
   } else {
      //process the output
      $output  = '<p';
      if (isset ($class)){
         $output .= ' class="'.$class.'"';
      }else{
         $output .= ' style="color: #FF2222;font-weight:bold;"';
      }
      $output.= '>'.$message.'</p>';
   }

   //check if we asked to assign the output to a variable.
   if (!empty($assign)) {
      $this->assign($assign, $output);
   } else {
      //did not ask that, returns the output
      return $output;
   }
}
?>
