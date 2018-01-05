#include <stdio.h>
#include <stdlib.h>

#define MAXBITS 16
#define MAXCHARS 21
#define MAXSYMBOLS 50

   typedef unsigned short set; 
	
	//Function List:
   int convert_text_to_binary_code(FILE *fp);
   void start_values();
   void labels();
   void numb_opcode();
   void numb_mode();
   void numb_register();
   void numb_address();
   void print_bits(set number);
   char pseudo_op_checker(char letters, FILE *fp);
   set convert_to_binary();
   int char_to_numb(char letter);
   void symbol_table_add(int first);
   int symbol_table_search(int first);
   void symbol_table_add_address(int first);
   void symbol_table_add_location(int first);
   void print_symbol_table();
   void error_table_print();
   void error_table_add(int found, int errorType);
   void direct_binary_address();
   void symbol_table_check();
   void delete_errors_symbol_table();
   void print_code();
   int undefined_table_search(int first);
	
	//Varaible List:
   set binary_instruction;
   int start;
   char letter_array [MAXCHARS];
   char program_array[100][MAXCHARS];
	//SYMBOL TABLE
   char symbol_table_labels [MAXSYMBOLS][6];
   int symbol_table_address [MAXSYMBOLS][10];
	//UNDEFINED SYMBOL TABLE
   char undefined_table [MAXSYMBOLS][6];
   int undefined_table_address [MAXSYMBOLS][5];
	//MULTIPLY DEFINED SYMBOL TABLE
   char multiply_defined_table [MAXSYMBOLS][6];
   int multiply_defined_table_address [MAXSYMBOLS][5];
   int symbols;
   int symbols_undefined;
   int symbols_multiply_defined;
   set program_counter;
   set main_memory [256]; 
   set registers [4];
   set condition_code;
   int halt_catch;
   int error;
   int pass;
   int error_type;
	
   int main()
   {
      int total_lines;
      start_values();
   
      FILE *fp = fopen("../instr/prog1b.dat","r");
   	
   	//if the file is not null it will read from it
      if(fp != NULL)
      {
         total_lines = convert_text_to_binary_code(fp);
      }//end if NULL
      else 
      {
         printf("ERROR -- FILE DOES NOT EXIST.");
      }//end else
   	
      symbol_table_check();
      delete_errors_symbol_table();
      direct_binary_address();
      print_code(total_lines);
      printf("\n\n\n");
      print_symbol_table();
      printf("\n\n");
      error_table_print();
      printf("\n\n\n");
   }//end of main
	
   int convert_text_to_binary_code(FILE *fp)
   {
      int i, p, lcv, lcv_two; 
      char letter;
      start = 0;
      lcv = 0;//letter counter
      lcv_two = 0;//line counter
   	
   	
      while ((letter = fgetc(fp)) != EOF && start != 4)
      {
         binary_instruction = 0;
         if(start != 4 && start != 0)
         {
            //stores in the array if less than array size
            //if not, then it searches for the end of the line
            if(lcv < MAXCHARS && letter != '\n')
            { 
               if(!isspace(letter))
               {  
                  if(letter == '.')
                  {
                     letter = pseudo_op_checker(letter, fp);
                     lcv += 3;
                  }   
                  else
                  {          
                     letter_array [lcv] = letter;
                  }
               }
               else
               {
                  letter_array [lcv] = ' ';
               }
               lcv++;
            }//end of if less than MAXCHARS
            else
            {
               while(letter != '\n')
               {
                  letter = fgetc(fp);
               }       
               
            	//fills an array with code
               for(p = 0; p < MAXCHARS; p++)
               {
                  program_array[lcv_two][p] = letter_array[p];
               }
            	
               convert_to_binary();
            	
               if(error == 0)
               {
                  main_memory[program_counter] = binary_instruction; 
                  program_counter++;
               }
            	       
              	//fills the array back with ' ' (spaces)
               for(p = 0; p < MAXCHARS; p++)
               {
                  letter_array[p] = ' ';
               }	
               
               lcv = 0;
               error = 0;
               lcv_two++;
            }
         }//end of if started
         else
         {
            //checks for . start
            if(letter == '.')
            {
               letter = pseudo_op_checker(letter, fp);
               //goes through until the new line to start the program
               if(start == 1)
               {	
                  while(letter != '\n')
                  {
                     letter = fgetc(fp);
                  }
               }
            }//end of pseudo op
         }//end else
      }//end while
      return lcv_two;
   }//end of convert_text_to_binary_code
	
   void start_values()
   {
      int i, p, lcv, lcv_two; 
   	
   	//fills letter_array with ' ' (spaces) 
      for(p = 0; p < MAXCHARS; p++)
      {
         letter_array[p] = ' ';
      }	
   	
   	//fills symbol_table_labels with 00
      for(lcv; lcv < MAXSYMBOLS; lcv++)
      {
         for(lcv_two; lcv_two < 6; lcv_two++)
         {
            symbol_table_labels[lcv][lcv_two] = 00;
         }//second for loop
         lcv_two = 0;
      }//first for loop
   	
   	//fills symbol_table_addresss with 00
      for(lcv; lcv < MAXSYMBOLS; lcv++)
      {
         for(lcv_two; lcv_two < 10; lcv_two++)
         {
            symbol_table_address[lcv][lcv_two] = 00;
         }//second for loop
         lcv_two = 0;
      }//first for loop
		
   	//sets all values to zero
      symbols = 0;
      symbols_undefined = 0;
      symbols_multiply_defined = 0;
      program_counter = 0; 
      error = 0;
      pass = 0;
   }
	
   void print_code(int total_lines)
   {
      int p, f;
      char letter;
   	
      pass = 1;
      program_counter = 0;
      f= 0;
   	
      printf("PC\tLabels\t OP\tMode\tReg\tAddr\tInstruction\t\tErrors\n");
      printf("-------------------------------------------------------------------------------\n");
   
   	//prints out array
      for(f; f < total_lines; f++)
      {    
         binary_instruction = 0;
         error = 0;
         error_type = -1;
         for(p = 0; p < MAXCHARS; p++)
         {
            letter = program_array[f][p];
            letter_array[p] = letter;
         }//end of second for loop
         convert_to_binary();
         if(error == 0)
         {
           	//program_counter++;
            printf("%04i\t", program_counter);
            binary_instruction = main_memory[program_counter];
            program_counter++;
         }
         else
         {
            printf("\t");
         }
         for(p = 0; p < MAXCHARS; p++)
         {
            letter = program_array[f][p];
            if(p == 6 || p == 10 || p == 12 || p == 14)
            {
               printf("\t");
            }
            if(letter != 0)
            {            
               printf("%c", letter);
            }
         }//end of print for loop
         if(error == 0)
         {
            printf("\t");
            print_bits(binary_instruction);
            if(error_type != -1)
            {
               if(error_type == 1)
               {
                  printf("\tMMMMM");
               }
               if(error_type == 2)
               {
                  printf("\tUUUUU");
               }
            }
         }
         else
         {
            printf("\t???? ???? ???? ????");
         }
         printf("\t\t");
         printf("\n");
      }//end of first for loop
      //error_type = 1 'M'
   	//error_type = 2 'U'	
   }
	
   void labels()
   {
      if(error == 0 && pass ==0)
      {    
         if(letter_array[0] != ' ')
         {
            symbol_table_add(0);
            symbol_table_add_address(0);
         }
      }
   }
	
   void numb_opcode()
   {
      set temp;
      temp = 0;
   	    
      if(letter_array[7] == 'L' && letter_array[8] == 'O'&& letter_array[9] == 'D')
      {
         temp += 0; 
      }
      else if(letter_array[7] == 'S' && letter_array[8] == 'T'&& letter_array[9] == 'O')
      {
         temp += 1;
      }
      else if(letter_array[7] == 'A' && letter_array[8] == 'D'&& letter_array[9] == 'R')
      {
         temp += 2;
      }
      else if(letter_array[7] == 'S' && letter_array[8] == 'U'&& letter_array[9] == 'R')
      {
         temp += 3;
      }
      else if(letter_array[7] == 'A' && letter_array[8] == 'N'&& letter_array[9] == 'D')
      {
         temp += 4;
      }
      else if(letter_array[7] == 'I' && letter_array[8] == 'O'&& letter_array[9] == 'R')
      {
         temp += 5;
      }
      else if(letter_array[7] == 'N' && letter_array[8] == 'O'&& letter_array[9] == 'T')
      {
         temp += 6;
      }
      else if(letter_array[7] == 'C' && letter_array[8] == 'M' && letter_array[9] == 'P')
      {
         temp += 7;
      }
      else if(letter_array[7] == 'C' && letter_array[8] == 'L'&& letter_array[9] == 'R')
      {
         temp += 8;
      }
      else if(letter_array[7] == 'J' && letter_array[8] == 'M'&& letter_array[9] == 'P')
      {
         temp += 9;
      }
      else if(letter_array[7] == 'J' && letter_array[8] == 'E'&& letter_array[9] == 'Q')
      {
         temp += 10;
      }
      else if(letter_array[7] == 'J' && letter_array[8] == 'G'&& letter_array[9] == 'T')
      {
         temp += 11;
      }
      else if(letter_array[7] == 'J' && letter_array[8] == 'L'&& letter_array[9] == 'T')
      {
         temp += 12;
      }
      else if(letter_array[7] == 'H' && letter_array[8] == 'L'&& letter_array[9] == 'T')
      {
         temp += 15;
      }   
      else if(letter_array[7] == '.' && letter_array[8] == 'd' && letter_array [9] == 'w')
      {
         //checkopcode
      }
      else if(letter_array[7] == '.' && letter_array[8] == 'd' && letter_array [9] == 's')
      {
         //checkopcode
      }
      else
      {      
         error++;
      }
   	
      binary_instruction += temp << 12;
   
   }
   void numb_mode()
   {
      set temp;
      temp = 0;
   	
      if(letter_array[11] == 'I')
      {
         temp += 1; 
      }
      else
      {
         temp += 0;
      }
   	
      temp = temp << 11;
      binary_instruction += temp;
   }
   void numb_register()
   {
      set temp;
      temp = 0;
   	  
      if(letter_array[13] == '0')
      {
         temp += 0; 
      }
      else if(letter_array[13] == '1')
      {
         temp += 1; 
      }
      else if(letter_array[13] == '2')
      {
         temp += 2; 
      }
      else if(letter_array[13] == '3')
      {
         temp += 3; 
      }
      else
      {
         temp += 0;
      }
      temp = temp << 8;
      binary_instruction += temp;
   	
   }
   void numb_address()
   {
      char letter, i;
      int number_array [6];
      int number, sum, lcv, lcv_two;
      sum = 0; 
      lcv = 0; 
      i = 16;
   	
   	//take and put into array
      if(error == 0)
      {
         if(letter_array[11] == 'I')
         {
         //make sure it is signed integer 
            if(letter_array[15] == '#')
            {
               if(letter_array[i] == 'R')
               {
                  i++;
               }          
               letter = letter_array[i];
               while(letter != ' ')
               {
                  number = char_to_numb(letter);
                  number_array[lcv] = number;          
               
                  lcv++;
                  i++;
                  letter = letter_array[i];
               }
               lcv_two = 0;
               lcv--;
            	//converts number_array into int form by factors of ten
               while(lcv >= 0)
               {
                  number = number_array[lcv]; 
                  if(lcv_two != 0)
                  {
                     number = number * lcv_two;
                  }
                  sum += number;
                  lcv_two += 10;
                  lcv--;
               }
               binary_instruction += sum; 
            }//if a signed integer 
         }//if immeadiate 
         else
         {
            if(letter_array[7] == 'C' && letter_array[8] == 'M' && letter_array[9] == 'P')
            {
            	//does nothing
            }
            else if(letter_array[7] == 'C' && letter_array[8] == 'L'&& letter_array[9] == 'R')
            {
            	//does nothing
            }    
            else if(letter_array[7] == 'H' && letter_array[8] == 'L'&& letter_array[9] == 'T')
            {
            	//does nothing
            } 
            else if(letter_array[7] == '.' && letter_array[8] == 'd' && letter_array [9] == 'w')
            {
            	//does nothing        
            }
            else if(letter_array[7] == '.' && letter_array[8] == 'd' && letter_array [9] == 's')
            {
            	//does nothing         
            }
            else
            {
               if(pass == 0)
               {
                  symbol_table_add(15);
                  symbol_table_add_location(15);
               }
               if(pass == 1)
               {
               	//check tables
                  int find, findu;
                  find = symbol_table_search(15);
                  if(find == -1)
                  {
                  	//check undefined table
                     findu = undefined_table_search(15);
                  	
                     if(findu == -1)
                     {
                        error_type = 1;
                     }
                     else
                     {
                        error_type = 2;
                     }
                  	//check multiply defined table
                  	
                  }
               }//end of pass = 1       	
            }
         }
      }
   }//end of numb_address
	
   int char_to_numb(char letter)
   {
      int temp;
   	
      if(letter == '0')
      {
         temp = 0;
      }
      else if(letter == '1')
      {
         temp = 1; 
      }
      else if(letter == '2')
      {
         temp = 2; 
      }
      else if(letter == '3')
      {
         temp = 3; 
      }
      else if(letter == '4')
      {
         temp = 4; 
      }
      else if(letter == '5')
      {
         temp = 5; 
      }
      else if(letter == '6')
      {
         temp = 6; 
      }
      else if(letter == '7')
      {
         temp = 7; 
      }
      else if(letter == '8')
      {
         temp = 8; 
      }
      else if(letter == '9')
      {
         temp = 9; 
      }
      else
      {
         printf("ERROR--not a number");
      }
      return temp; 
   }//end char_to_numb
	
   void print_bits(set number)
   {
      set numberTwo;//another unsigned long
      int spaces = 0;//helps count the spaces
      numberTwo = 1 << (sizeof(number) * 8 -1);
   	
      while (numberTwo > 0)
      {
         if(number & numberTwo)
         {
            printf("1");
         }//end if 1
         else 
         {
            printf("0");
         }//end if 0
         numberTwo >>= 1;
         ++spaces;
      	//every four bit a ' ' (space) is printed 
         if(spaces == 4)
         {
            printf(" ");
            spaces = 0;
         }
      }//end while
   }//end print_bits
	
   char pseudo_op_checker(char letter_dot, FILE *fp)
   {
      int i, j;
      j = 7;    
      char letters [3];
      letters [0] = letter_dot;
      letter_array[j] = letter_dot;
      i = 1; 
   	
      //gets the rest of .**
      while(!isspace(letter_dot = fgetc(fp)))
      {
         j++;
         letters [i] = letter_dot;
         letter_array[j] = letter_dot;
         i++;
      }
      j++;
      letter_array[j] = letter_dot;
      int pseudo_op_code;
      if(letters[0] == '.' && letters[1] == 's' && letters [2] == 't')
      {
         pseudo_op_code = 1;
      }
      else if(letters[0] == '.' && letters[1] == 'd' && letters [2] == 'w')
      {
         pseudo_op_code = 2;
      }
      else if(letters[0] == '.' && letters[1] == 'd' && letters [2] == 's')
      {
         pseudo_op_code = 3;
      }
      else if(letters[0] == '.' && letters[1] == 'n' && letters [2] == 'd')
      {
         pseudo_op_code = 4;
      }
      else
      {
         printf("ERROR -- pseudo op");
         pseudo_op_code = -1;
      }
      start = pseudo_op_code;
      return letter_dot;
   }//end of pseudo_op_checker
	
   set convert_to_binary()
   {
      numb_opcode();
      labels();    
      numb_mode();
      numb_register();
      numb_address();
   }//end of convert_to_binary
	
   void symbol_table_add(int first)
   {
      char letter;
      int i, lcv, found;
      i = first;
      lcv = 0;
      found = symbol_table_search(first);
		
      if(found == -1)
      {
         letter = letter_array[first];
         while(lcv < 6 && letter != ' ' && letter != ',')
         {
            symbol_table_labels[symbols][lcv] = letter;
            first++;
            letter = letter_array[first];
            lcv++;
         }
         symbols++;
      }
   }//end of symbol_table_add
	
   int symbol_table_search(int first)
   {
      char letter, letter_two;
      int lcv, lcv_two, i, sum, j, found;
      lcv = 0;    
      lcv_two = 0;
      j = first;
      i = 0;
      found = -1;
   	
      for(lcv; lcv < MAXSYMBOLS; lcv++)
      {
         sum = 0;
         for(lcv_two; lcv_two < 6; lcv_two++)
         {
            letter = letter_array[first];
            letter_two = symbol_table_labels[lcv][lcv_two];
            if(letter == letter_two)
            {
               if(letter != 00)
               {          
                  sum++;
               }    
            }
            first++;
         }//second for loop
         if(sum >= 3)
         {
            found = lcv;
         }
         first = j;
         lcv_two = 0;
      }//first for loop
      return found;
   }//end of symbol_table_search
	
   int undefined_table_search(int first)
   {
      char letter, letter_two;
      int lcv, lcv_two, i, sum, j, found;
      lcv = 0;    
      lcv_two = 0;
      j = first;
      i = 0;
      found = -1;
   	
      for(lcv; lcv < MAXSYMBOLS; lcv++)
      {
         sum = 0;
         for(lcv_two; lcv_two < 6; lcv_two++)
         {
            letter = letter_array[first];
            letter_two = undefined_table[lcv][lcv_two];
         	
            if(letter == letter_two)
            {
               if(letter != 00)
               {          
                  sum++;
               }    
            }
            first++;
         }//second for loop
         if(sum >= 3)
         {
            found = lcv;
         }
         first = j;
         lcv_two = 0;
      }//first for loop
      return found;
   }//end of symbol_table_search
	
   void symbol_table_add_address(int first)
   {
      int found, number;
      found = symbol_table_search(first);
   	
      if(found != -1)
      {
         if(first == 0)
         {
            number = symbol_table_address[found][0];
            if(number == 0)
            {
               symbol_table_address[found][0]= program_counter;
            }
            else
            {
               number = symbol_table_address[found][0];
               error_table_add(number, 2);
            }
         }//means it a label
      }//mean it found it
   }//end of symbol_table_add_address
   
   void symbol_table_add_location(int first)
   {
      int number, lcv;
      int found;
      found = symbol_table_search(first);
      lcv = 1;
 
      if(found != -1)
      {
         number = symbol_table_address[found][lcv];
         while(number != 00)
         {
            lcv++;
            number = symbol_table_address[found][lcv];
         }
         symbol_table_address[found][lcv] = program_counter;
      }
   }//end add location
	
   void direct_binary_address()
   {
      int number, lcv, lcv_two;
      lcv = 0;
      lcv_two = 1;
   	
      for(lcv; lcv < MAXSYMBOLS; lcv++)
      {
         for(lcv_two; lcv_two < 10; lcv_two++)
         {
            number = symbol_table_address[lcv][lcv_two];
            if(number != 00)
            {
               main_memory[number] += symbol_table_address[lcv][0];
            }
         }//second for loop
         lcv_two = 1;
      }//first for loop
   }
	
   void print_symbol_table()
   {
      char letter;
      int number;
      int lcv, lcv_two,  empty;
      lcv = 0;
      lcv_two = 0;
      empty = 0;
   	
   	printf("\n      SYMBOL TABLE\n");
      printf("      ------------\n");
      printf("Label\t\tAddress\n");
      printf("----------\t----------\n");
      for(lcv; lcv < MAXSYMBOLS; lcv++)
      {
         for(lcv_two; lcv_two < 6; lcv_two++)
         {
            letter = symbol_table_labels[lcv][lcv_two];
            number = symbol_table_address[lcv][0];
            if(lcv_two == 0 && letter == 0)
            {
               empty = 1;
            }
            else
            {
               if(letter != 00)
               {
                  printf("%c", letter);
               
                  if(lcv_two == 5)
                  {
                     printf("\t");
                  }
               }//end if not equal to zero
               if(empty == 0)
               {
                  if(lcv_two == 5)
                  {
                     number = symbol_table_address[lcv][0]; 
                     printf("\t\t%i", number);
                     printf("\n");
                  }
               }
            }
         }//second for loop
         empty = 0;
         lcv_two = 0;
      }//first for loop
   }
	
   void symbol_table_check()
   {
      int number, lcv, lcv_two, i, sum;
      char letter, letter_two;
      sum = 0;
      lcv = 0;
      lcv_two = 0;
   	
      for(lcv; lcv < symbols; lcv++)
      {
         for(lcv_two; lcv_two < 2; lcv_two++)
         {
            number = symbol_table_address[lcv][lcv_two];
            letter = symbol_table_labels[lcv][0];   	
            if(lcv_two == 0)
            {
               if(number == 00)
               {
                  if(letter != 00)
                  {
                     error_table_add(lcv, 0);
                  }    
               }
            }//if address
            else
            {
               if(number == 00)
               {
                  if(letter != 00)
                  {
                     error_table_add(lcv, 1);
                  }    
               }
            }//if not used
         }//second for loop
         lcv_two = 0;
      }//first for loop
      lcv = 0; 	
   }//end of symbol_table_check
	
   void symbol_table_delete(int found)
   {
      int lcv = 0;
      for(lcv; lcv < 6; lcv++)
      {
         symbol_table_labels[found][lcv] = 00;
      }
      lcv = 0;
      for(lcv; lcv < 10; lcv++)
      {
         symbol_table_address[found][lcv] = 00;
      }
      symbols--;
   }//end of symbol_table_delete
	
   void error_table_add(int found, int error_type)
   {
   	//0-not defined
   	//1-not used
   	//2-multiply defined
   	
      char letter;
      int i,j, lcv, number, number_two, value;
      lcv = 0;
   	
      if(error_type == 0)
      {
         letter = symbol_table_labels[found][lcv];
         while(letter != 00)
         {
            undefined_table [symbols_undefined][lcv] = letter;
            lcv++;
            letter = symbol_table_labels[found][lcv];
         }
         lcv = 1;
         i = 0;
         for(lcv; lcv < 5; lcv++)
         {
            number = symbol_table_address[found][lcv];
            if(number != 0)
            {
               undefined_table_address [symbols_undefined][i] = number;
               i++;
            }
         }
         symbols_undefined++;
      }//end undefined
      else if(error_type == 1)
      {
      	//If a varaiable is not used
      }//end not used
      else if(error_type == 2)
      {
         int place = found;
         int start = symbols_multiply_defined;
      	
         for(lcv; lcv < symbols; lcv++)
         {
            number = symbol_table_address[lcv][0];
         	
            if(number == found)
            {
               found = lcv;
               lcv = symbols;
            }
         }
         lcv = 0;
         letter = symbol_table_labels[found][lcv];
      	
         for(lcv; lcv < symbols_multiply_defined; lcv++)
         {
            number = multiply_defined_table_address[lcv][0];
         	
            if(number == place)
            {
               letter = 00;
               start = lcv;
            }
         }
      	
         lcv = 0;
      	
         while(letter != 00)
         {
            multiply_defined_table [symbols_multiply_defined][lcv] = letter;
            lcv++;
            letter = symbol_table_labels[found][lcv];
         }
         i = 0;
         lcv = 1;
         
         //pc and found
         number = place;   
         number_two = program_counter; 
         value = multiply_defined_table_address[start][i]; 
      		
         if(value == 0)
         {
            multiply_defined_table_address[start][i] = number;
            i++;
            multiply_defined_table_address[start][i] = number_two;
         	
            symbols_multiply_defined+=2;
         }//end if value == 0
         else
         {
            while(value != 0){
               if(number == value)
               {
                  number = 0;
               }
               if(number_two == value)
               {
                  number = 0;
               }
               i++;         
               value = multiply_defined_table_address[start][i];   
            }
            if(number != 00)
            {
               multiply_defined_table_address[start][i]= number;
               i++;
               symbols_multiply_defined++;
            }
            if(number_two != 00)
            {
               multiply_defined_table_address[start][i]= number_two;
               symbols_multiply_defined++;
            }
         }//end else
      }//end multiply defined
      else
      {
         printf("ERROR--error type");
      }	
   }//end error_table_add
	
   void delete_errors_symbol_table()
   {
      int i, j, lcv, lcv_two, sum;
      char letter, letter_two;
      sum = 0;
      lcv = 0;
      lcv_two = 0;
      j = 0;
      i = 0;
      for(lcv; lcv < symbols_undefined; lcv++)
      {
         for(lcv_two; lcv_two < symbols; lcv_two++)
         {
            for(i; i < 6; i++)	
            {
               letter = symbol_table_labels[lcv_two][i];
               letter_two = undefined_table [lcv][i];
               if(letter == letter_two)
               {
                  sum++;
               }//same first letter
            }
            i = 0;
            if(sum == 6)
            {
            	//delete from symbol table
               symbol_table_delete(lcv_two);
            }
            sum = 0;
         }
         lcv_two = 0;
      }
      sum = 0;
      lcv = 0;
      lcv_two = 0;
      j = 0;
      i = 0;
      for(lcv; lcv < symbols_multiply_defined; lcv++)
      {
         for(lcv_two; lcv_two < symbols; lcv_two++)
         {
            for(i; i < 6; i++)	
            {
               letter = symbol_table_labels[lcv_two][i];
               letter_two = multiply_defined_table [lcv][i];
               if(letter == letter_two)
               {
                  sum++;
               }//same first letter
            }
            i = 0;
            if(sum == 6)
            {
            	//delete from symbol table
               symbol_table_delete(lcv_two);
            }
            sum = 0;
         }
         lcv_two = 0;
      }   	
   }//end of delete errors symbol table
	
   void error_table_print()
   {
      char letter;
      int number;
      int lcv, lcv_two, lcv_three, empty;
      lcv = 0;
      lcv_two = 0;
      lcv_three = 0;
      empty = 0;
   	
      printf("\n     UNDEFINED TABLE\n");
      printf("     ---------------\n");
      printf("Label\t\tLine Number[s]\n");
      printf("----------\t----------\n");
      for(lcv; lcv < MAXSYMBOLS; lcv++)
      {
         for(lcv_two; lcv_two < 6; lcv_two++)
         {
            letter = undefined_table[lcv][lcv_two];
            
				if(lcv == 0 && lcv_two == 0 && letter == 0)
				{
					printf("NO ERRORS");
				}
				
				if(lcv_two == 0 && letter == 0)
            {
               empty = 1;
            }
            else
            {
               if(letter != 00)
               {
                  printf("%c", letter);
               }//end if not equal to zero
               if(empty != 1)
               {
                  if(lcv_two == 5)
                  {
                     for(lcv_three; lcv_three < 5; lcv_three++)
                     {
                        number = undefined_table_address[lcv][lcv_three];
                     
                        if(number != 0)
                        {
                           printf("\t\t%i\n", number);
                        }
                     }
                     lcv_three=0;
                  }
                  if(lcv_two == 10)
                  {
                     printf("\n");
                  }
               }
            }
         }//second for loop
         lcv_two = 0;
      }//first for loop
      lcv = 0;
      empty = 0;
      printf("\n\n");
      printf("\n\n\n   MULTIPLY DEFINED TABLE\n");
      printf("   ---------------------\n");
      printf("Label\t\tLine Number[s]\n");
      printf("----------\t------------\n");
      for(lcv; lcv < MAXSYMBOLS; lcv++)
      {
         for(lcv_two; lcv_two < 6; lcv_two++)
         {
				letter = multiply_defined_table[lcv][lcv_two];
				
				if(lcv == 0 && lcv_two == 0 && letter == 0)
				{
					printf("NO ERRORS");
				}
				
            if(lcv_two == 0 && letter == 0)
            {
               empty = 1;
            }
            else
            {
               if(letter != 00)
               {
                  printf("%c", letter);
               }//end if not equal to zero
               if(empty != 1)
               {
                  if(lcv_two == 5)
                  {
                     for(lcv_three; lcv_three < 5; lcv_three++)
                     {
                        number = multiply_defined_table_address[lcv][lcv_three];
                     
                        if(number != 0)
                        {
                           printf("\t\t%i\n", number);
                        }
                     }
                     lcv_three = 0;
                  }
                  if(lcv_two == 10)
                  {
                     printf("\n");
                  }
               }
            }
         }//second for loop
         lcv_two = 0;
         empty =0;
      }//first for loop
   }//end print_error_table
