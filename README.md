#Simple PHP Color Library


##Factory method to generate color
 Takes many types:
 - Object - only instance of TColor - clone it
 - numeric (flot, integer), if float makes from [ 0 - 1 ] if integer makes from [ 0 - 255 ]
 - Array with keys:
          r,g,b
          c,m,y,k
          h,s,v
          l,a,b
      else it takes recursivly factory on all values in array
 - String, it match many patterns (all spaces and new lines are removed from string):
 

 ###HTML Colors
 - ####\#anything
 + goes to HEXa parser that search for #XXX or #XXXXXX (doesnt matter on letters size)
 - ####XXX or XXXXXX 
 + where X can be 0-9 or a-f (doesnt matter on letters size) - goes to HEXa parser
 + Example: TColor::factory('#aaa'), TColor::factory('aa0'), TColor::factory('000000'), TColor::factory('#ab1200') - all will be matched as HTML HEX color
 
 ###RGB(A) Colors
 - ####RGB(X,X,X)
 - ####RGBA(X,X,X,X) 
   X can be 0% - 100% or 0 - 255
 + Example: TColor::factory('RGB(0, 10, 100)');
 
 ###CMYK Colors
 - ####CMYK(X,X,X,X) 
   X can be 0.0 - 1.0 or 0% - 100%
 + Example: TColor::factory('CMYK(0, 0.2, 0.1, 0.8)');
 
 ###Float(A) Colors
 - ####F(X,X,X)
 - ####FA(X,X,X,X) 
   X can be 0.0 - 1.0
 + Example: TColor::factory('FA(0.1, 0.1, 0.5, 1.0)');
 
 ###HSV Colors
 - ####HSV(X,X,X) 
   X can be 0.0 - 1.0
 + Example: TColor::factory('HSV(0.1, 0.5, 1)');

 ###LAB Colors
 - ####LAB(X,X,X) 
   X can be 0.0 - 1.0
 + Example: TColor::factory('LAB(0.1, 0.2, 0.3)');
 
 ###Random Colors
 - #### random("X","X")
   X can be anything above
 + Min and Max color betwen random will be
 + Example: TColor::factory('random("#a00","RGB(0, 100, 100)")');
 + Example: TColor::factory('random("a00","000")');
 <br>
 - ####randomvar("X","X")
   X can be anithing above
 + Mean and Variance of random color
 + Example: TColor::factory('randomvar("#aaa","#a00")');
 <br>
 - ####randomval("X")
   X can be anithing above
 + random value (brightness) of color X
 + Example: TColor::factory('randomval("#aaa")');
 <br>
 - ####randomsat("X")
   X can be anithing above
 + random saturation of color
 + Example: TColor::factory('randomsat("HSV(0.5, 0.1, 1)")');
 <br>
 - ####randomfull
 + generate random color with saturation and brightness set to one
 + Example: TColor::factory('randomfull');
 
 ###Bright of color
 - ####val("X","Y") 
 + X can be anything above and Y is number 0.0 - 1.0
 + Example: TColor::factory('val("#a00", "0.1")');