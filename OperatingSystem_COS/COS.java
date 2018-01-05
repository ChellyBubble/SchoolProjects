/* Chelsea Kochan
   CSC 341 -OS
   2/10/2014
   
   COS- Chelsea's Operating System 
   Version 2 
*/
import java.io.*;
import java.util.*;

public class COS {
   public static void main (String[]args){
      
      OperatingSystem osStart = new OperatingSystem();
   
   }
}

class OperatingSystem {
   
   Machine opHardware;
   User userArray [];
   Scanner userInput;
   int clock;
   int MAX_USERS = 3;
   
   OperatingSystem(){
      //boots up operating system 
      bootOS();
      
      //Schedualer starts
      runSchedualer();
      
   }
   
   void bootOS(){
      //booting up memory
      opHardware = new Machine();
      
      //Sets up user varaibles 
      userSetUp();
      
      //load program
      loadProgram(userArray[0]);
      loadProgram(userArray[1]);
      
      instructions();
      
   }
   
   void userSetUp(){
   
      userInput = new Scanner(System.in);
      User userOne = new User("USER 1", false, "proj2a.txt", (short)1);
      User userTwo = new User("USER 2", false, "proj2b.txt", (short)2);
      User userOS = new User("OS", true);
      
      userArray = new User [MAX_USERS];
      
      userArray[0] = userOne;
      userArray[1] = userTwo;
      userArray[2] = userOS;
      
      clock = 0; 
   }
   
   void instructions(){
      System.out.println("Welcome to COS, Chelsea's Operating System! \n\nUser Commands are: \n\trun \n\tnop \n\nOS Commands are: \n\tdmp \n\tnop \n\tstp \n"); 
   }
   
   void loadProgram(User cur){
      //try/catch if file is not found
      try{
         //gets file name from main and makes it the file.
         File fileInput = new File(cur.getFile());
         //sets up scanner to read from the file. 
         Scanner readsFile = new Scanner(fileInput);
         
         String hexStart = readsFile.next();//memory start location 
         short numberStart = hexToShort(hexStart);
         cur.setStartLine(numberStart);
         
         int lines = (int)numberStart; //counts lines to know where to put it in memory
         
         while(readsFile.hasNext()){          
            
            String hexRead = readsFile.next();//eading in next String setting it to hexRead
            
            if(hexRead.length() == 4){
            
               short numberMM = hexToShort(hexRead);//numberMM - number main memory
               //hex to short int number then store in memory
               opHardware.setMainMemory(lines, 0, numberMM); 
            }
            else{
               short numMM = hexToShort(hexRead);//numMM - number main memory
               opHardware.setMainMemory(lines, 0, numMM); 
            }
            lines++;
         }//end while 
         cur.setEndLine((short)lines); 
         
      }//end try
      catch(FileNotFoundException IO){
         System.out.println("ERROR--- cannot find pre-loaded file."); 
      }//end catch
      //opHardware.printMainMemory();//memory dump
      //opHardware.printRegisters();
   }//end load program
   
   private short hexToShort(String hex){
      String temp = "";
      String bin;
      String binFragment = "";
      int iHex;
      hex = hex.trim();
      hex = hex.replaceFirst("0x", "");
   
      for(int i = 0; i < hex.length(); i++){
         iHex = Integer.parseInt(""+hex.charAt(i),16);
         binFragment = Integer.toBinaryString(iHex);
      
         while(binFragment.length() < 4){
            binFragment = "0" + binFragment;
         }
         temp += binFragment;
      }
      bin = temp;
      
      char bit;
      int multiplier = 0;
      short number = 0;
      int counter; 
      counter = bin.length();
      bit = bin.charAt(counter-1);
      
      while (counter >= 0){
         
         if(bit == '1')
         {
            number += Math.pow(2,multiplier); 
         }
         else if(bit == '0')
         {
            //nothing
         }
         multiplier++;
         counter--;
         if(counter == 0){
            break;
         }      
         bit = bin.charAt(counter-1);
         
      }//end while
      return number;
   }//end of hexToShort
   
   void runSchedualer(){
      
      User current;
      User last; 
      int lcv = 0;
      boolean end; 
      
      last = userArray[lcv];
      
      do{
         
         current = userArray[lcv];
         
         //check to see if the user is already running 
         if(current.getRunning() != true){
            getCommand(current);
         }
         else{
            runProg(current, false); 
            checkDeadlock(current, last); 
         }
         
         end = current.getStop();
         
         if(lcv == 2){
            lcv = 0;
         }
         else{
            lcv++;
         } 
         
         System.out.println();
         
         last = current;
         
      }while(end != true);
      
   }
   
   void getCommand(User cur){
      String command;
      boolean commandsProcessed = false;
      int ticks = 0;
      
      while(commandsProcessed == false || ticks < 1){
      
         System.out.println(cur.getName() + " PLEASE ENTER A COMMAND:");
         command = userInput.next();
      
         if(command.equals("run")){
            commandsProcessed = runProg(cur, true);
         }
         else if(command.equals("dmp")){
            commandsProcessed = dmp(cur);
         }
         else if(command.equals("nop")){
            commandsProcessed = nop(cur);
         }
         else if(command.equals("stp")){
            commandsProcessed = stp(cur);
         }
         else{
            System.out.println("Invalid Command");
         }
         
         ticks = (clock/4);
         
      }//end while loop
   }//end get command 
   
   boolean runProg(User cur, boolean first){
      boolean comOS;
      comOS = cur.getOS();
      
      //if allowed to use command
      if(comOS != true){
      
         //first time
         if(first == true){
            cur.setRunning(true);
            fileLocks(cur);
            clock = opHardware.run(cur, clock);
            if((clock%4)!= 0){
               clock++;
               System.out.println(clock);
            }
                
         }//for first time running
         else{
            System.out.println("\n" + cur.getName() + " RUNNING PROGRAM.");
            clock = opHardware.run(cur, clock);
            if((clock%4)!= 0){
               clock++;
            }
         }//still running
         
         return true; 
         
      }//if is a user
      else{
         System.out.println("Invalid Command: Permissions REQUIRED");
         return false;
      }
   }
   
   boolean dmp(User cur){
   
      boolean comOS;
      comOS = cur.getOS();
      
      
      if(comOS == true){
         opHardware.printMainMemory();
         opHardware.printRegisters();
         clock += 4;
         return true; 
      }//if is a user
      else{
         System.out.println("Invalid Command: Permissions REQUIRED");
         return false; 
      }
   }
   
   boolean nop(User cur){
      clock += 4; 
      return true; 
   }
   
   boolean stp(User cur){
      boolean comOS;
      comOS = cur.getOS();
       
      
      if(comOS == true){
         cur.setStop(true);
         opHardware.printMainMemory();
         opHardware.printRegisters();
         System.out.println("\n\n\nCOS--- Terminating...");
         System.out.println("\nEND OF PROGRAM");
         clock += 4;
         return true; 
      }//if is a user
      else{
         System.out.println("Invalid Command: Permissions REQUIRED");
         return false;
      } 
   }
   
   void fileLocks(User cur){
      //lock everything in file
      short first;
      short last;
      short lock;
       
       //first = cur.getStartLine();
      last = cur.getEndLine();
      lock = cur.getLockNumber();
       
      for(first = cur.getStartLine(); first <last; first++){
         if(opHardware.getMainMemory(first, 1) == (short)0){
            opHardware.setMainMemory(first, 1, lock);
         }
      }
      
   }//data locks
   
   void checkDeadlock(User cur, User last){
      //checks for deadlock, if been waiting too long 
      
      int lcv; 
      int tempOne;
      int tempTwo;
      
      tempOne = cur.getWaitNumber();
      tempTwo = last.getWaitNumber();
      
      if(tempOne >= 2){
         if(tempTwo >= 2){
            System.out.print("\n\n------------------------------------\n");
            System.out.print("PROBLEM DETECTED: POSSIBLE DEADLOCK\n");  
            //running set to false? 
            if(tempOne > tempTwo){
               cur.reset();
               opHardware.dataUnlocks(cur);
               System.out.printf(cur.getName() + " has been reset.\n" + cur.getName() + " can enter commands next turn."); 
            }
            else{
               last.reset(); 
               opHardware.dataUnlocks(last);
               System.out.printf(last.getName() + " has been reset.\n" + last.getName() + " can enter commands next turn."); 
            }
           System.out.print("\n------------------------------------\n\n");
           
         }   
      }//end if tempOne greater than two
      
   }

   
}

class Machine{
   
   short registers []; //registers 0, 1, 2, 3
   short mainMemory [][]; //16-bit word addressable 
   short disk []; //disk not used yet
   short conditionCode; 
   
   Machine(){
      registers = new short [4];
      mainMemory = new short [256][2];
      disk = new short [16];
      
      int lcv, lcv2;
   
      //set up registers to be zero.
      for(lcv = 0; lcv < 4; lcv++){ 
         registers[lcv]= 0;
      }
      
      //set up main memory to be zero. 
      for(lcv = 0; lcv < 256; lcv++){
         for(lcv2 = 0; lcv2 < 2; lcv2++){
            mainMemory[lcv][lcv2] = 0;
         }
      }
      
      //set disk to be zero.
      for(lcv = 0; lcv < 16; lcv++){
         disk[lcv] = 0;
      }
         
      //set condition code to zero.
      conditionCode = 0;
      
      //set program counter to zero. 
      //programCounter = 0;
   }
   
   void setMainMemory(int lines, int row, short data){
      mainMemory[lines][row] = data;
   }

   short getMainMemory(int lines, int row){
      return mainMemory[lines][row];
   }

   void setDisk(int tempDisk, short number){
   
   }
   
   short getDisk(){
      return 0;
   }
   
   void printRegisters(){
      int lcv;
      
      System.out.println("\n\n\t\t\tREGISTER DUMP");  
      System.out.println("----------------------------------------------------------");
      for(lcv = 0; lcv < 4; lcv++){
         System.out.printf("R[%d]: %04X\t", lcv, registers[lcv]);
      }
      System.out.println("\n\n\n");
      
      //System.out.printf("\t\tPC: %04X\t", programCounter);
      System.out.printf("Condition Code: %04X\n\n", conditionCode);
   }
   
   void printMainMemory(){
      int lcv;
      System.out.println("\n\n\t\t\tMAIN MEMORY DUMP");
      System.out.println("-------------------------------------------------------------");
      System.out.println("0 = UNLOCKED\t1 = LOCKED BY USER 1\t2 = LOCKED BY USER 2");
      System.out.println("-------------------------------------------------------------");
      
      for(lcv = 0; lcv < 256; lcv++){
         System.out.printf("MM[%03d]: %04X\t LOCK: %X\t", lcv, mainMemory[lcv][0], mainMemory[lcv][1]);
         
         if((lcv+1)%2 == 0){
            System.out.println();
         }
      }
      System.out.println("\n");
   }
   
   void printDisk(){
   
   }
   
   int run(User cur, int clock){ 
      int lcv;
      int counter;     
      int lcvTwo; 
      short value;
      short valueTwo; 
      int total;
      short totalTwo;
      short temp;
      short tempOp;
      short tempMode;
      short tempRegister;
      short tempAddress;
      short tempPC;
      boolean memoryLocked; //checks if data is locked
      
      counter = 0; 
      tempOp = 0;
      
      //setting registers to user
      for(lcv = 0; lcv < 4; lcv++){ 
         registers[lcv] = cur.getRegSaved(lcv);
      }
      
      //checking to see if first insturtion can be run 
      tempPC = cur.getProgramCounter();
      temp = mainMemory[tempPC][0];
      
      memoryLocked = dataLocksChecker(cur, tempPC);
      
      tempAddress = getAddress(temp);
      memoryLocked = dataLocksChecker(cur, tempAddress);
      
      if(memoryLocked ==false){
         System.out.printf("\nSTEP BY STEP --- Running...\n\n");
         System.out.printf("PC = #\t\tOpcode\tR[X]\tData\tCC/MM[XXX]\n");
         System.out.printf("--------------------------------------------------");
      }
      
      //do until HALT or if clock is 4
      while(tempOp != -1 && counter != 4 && memoryLocked != true){
      
         temp = mainMemory[tempPC][0];	
         memoryLocked = dataLocksChecker(cur, tempPC);
         
         tempOp = getOpcode(temp);
         tempMode = getAddressingMode(temp);
         tempRegister = getRegister(temp);
         tempAddress = getAddress(temp);
      	
         tempPC++;
         
         
         if(tempOp == 0){
            
            memoryLocked = dataLocksChecker(cur, tempAddress);
            if(memoryLocked == false){
            
               if(tempMode == 0){
                  tempAddress = directAddressing(tempAddress);				
               }//D
               if(tempMode == 1){
                  tempAddress = indexedAddressing(tempAddress,tempRegister);
               }//I
            
               registers[tempRegister] = tempAddress; 
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("LOD\tR[%d]\t%d", tempRegister, registers [tempRegister]);
               counter++;
            }
         }//LOD
         else if(tempOp == 1){
            
            value = 0;
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){
            
               if(tempMode == 1){
               //indexedAddressing(tempAddress,tempRegister);
               }//D
            
            
               value = registers[tempRegister];
               mainMemory[tempAddress][0] = value;
               
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("STO\tR[%d]\t%d\tMM[%03d]", tempRegister, registers[tempRegister], tempAddress);
            
               counter++;
            }
         }//STO
         else if(tempOp == 2){
            value = 0; 
            valueTwo = 0;
            
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){
            
               if(tempMode == 0){
                  tempAddress = directAddressing(tempAddress);
               }//D
            
               value = registers[tempRegister];
               valueTwo = tempAddress; 
            
               total = value + valueTwo;
               totalTwo = (short)total;
                        
               registers[tempRegister] = totalTwo;
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("ADD\tR[%d]\t%d", tempRegister, registers[tempRegister]);
               
               counter++;
            }
         }//ADD
         else if(tempOp == 3){
            value = 0; 
            valueTwo = 0;
            
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){
            
               if(tempMode == 0){
                  tempAddress = directAddressing(tempAddress);
               }//D
            
            
            
               value = registers[tempRegister];
               valueTwo = tempAddress; 
            
               total = value - valueTwo;
               totalTwo = (short)total;
                        
               registers[tempRegister] = totalTwo;
            
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("SUB\tR[%d]\t%d", tempRegister, registers[tempRegister] );
            
               counter++;
            }
         }//SUB
         else if(tempOp == 4){
            System.out.printf("NOP");
            
            counter++;
         }//NOP
         else if(tempOp == 6){
            value = 0; 
            valueTwo = 0;
            
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){
            
               if(tempMode == 0){
                  tempAddress = directAddressing(tempAddress);
               }//D
            
               value = registers[tempRegister];
               valueTwo = tempAddress; 
            
               total = value & valueTwo;
               totalTwo = (short)total;
                        
               registers[tempRegister] = totalTwo;
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("AND\tR[%d]\t%d", tempRegister, registers[tempRegister] );
            
               counter++;
            }
         }//AND
         else if(tempOp == 7){
         
            value = 0; 
            valueTwo = 0;
            
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){
            
               if(tempMode == 0){
                  tempAddress = directAddressing(tempAddress);
               }//D
            
               value = registers[tempRegister];
               valueTwo = tempAddress; 
            
               total = value | valueTwo;
               totalTwo = (short)total;
                        
               registers[tempRegister] = totalTwo;
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("OR\tR[%d]\t%d", tempRegister, registers[tempRegister] );
            
               counter++;
            }
         }//OR
         else if(tempOp == -8){
         
            total = ~registers[tempRegister];
            totalTwo = (short)total;
                        
            registers[tempRegister] = totalTwo;
            
            System.out.printf("\nPC = %d : \t", (tempPC-1));
            System.out.printf("NOT\tR[%d]\t%d", tempRegister, registers[tempRegister] );
            
            counter++;
         } //NOT
         else if(tempOp == -7){
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){
            
               if(tempMode == 1){
                  tempAddress = indexedAddressing(tempAddress,tempRegister);
               }//I
            
            
               tempPC = tempAddress; 
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("JMP");
            
               counter++;
            }
         }//JMP
         else if(tempOp == -6){
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){
               if(conditionCode == 0){
               
                  if(tempMode == 1){
                     tempAddress = indexedAddressing(tempAddress,tempRegister);
                  }//I
               
                  tempPC = tempAddress; 
                
               }
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("JEQ");
            
               counter++;
            }
         }//JEQ
         else if(tempOp == -5){
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){
               if(conditionCode == 1){
                  if(tempMode == 1){
                     tempAddress = indexedAddressing(tempAddress,tempRegister);
                  }//I
               
                  tempPC = tempAddress;  
               }
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("JGT");
            
               counter++;
            }
         }//JGT
         else if(tempOp == -4){
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){
               if(conditionCode == 2){
                  if(tempMode == 1){
                     tempAddress = indexedAddressing(tempAddress,tempRegister);
                  }//I
               
                  tempPC = tempAddress; 
               }
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("JLT");
            
               counter++;
            }
         }//JLT
         else if(tempOp == -3){
            value = 0;
            value = registers [tempRegister]; 
            
            memoryLocked = dataLocksChecker(cur, tempAddress);
            
            if(memoryLocked == false){ 
               conditionCodes(value, tempAddress); 
               System.out.printf("\nPC = %d : \t", (tempPC-1));
               System.out.printf("CMP\tR[%d]\t\tCC:%d", tempRegister,  conditionCode);
            
               counter++;
            }
         }//CMP
         else if(tempOp == -2){
         
            registers[tempRegister] = 0;
            System.out.printf("\nPC = %d : \t", (tempPC-1));
            System.out.printf("CLR\tR[%d]", tempRegister);
         	
            counter++;
         }//CLR
         else if(tempOp == -1){
            System.out.printf("\nPC = %d : \t", (tempPC-1));
            System.out.printf("HLT");
            cur.setRunning(false);
            dataUnlocks(cur);
            cur.setWaitNumber(0);  
         }//HLT
         else{
            System.out.printf("\t\tERROR");
         }
         
         cur.setProgramCounter(tempPC);
            
      }//end while 
      if(memoryLocked == true){
         System.out.printf("\nThe peice of memory you are trying to access is currently locked by another user.\nPlease wait...");
         
         int waitCount;
         waitCount = cur.getWaitNumber();
         waitCount++;
         cur.setWaitNumber(waitCount);
      }
      
      if(counter == 4){
         clock += counter; 
      }
      else{
         clock += counter;
      }
      
      for(lcv = 0; lcv < 4; lcv++){ 
         cur.setRegSaved(lcv, registers[lcv]);
      }
      
      System.out.println();      
      return clock; 
      
      
   }
   
   short getOpcode (short instruction){	
      short tempTwo;
      short temp; 
      int total; 
      
      tempTwo = instruction;
      temp = instruction;	
      total = temp >> 12;
   	
      if(total == 13 || total == 14){
         total = 16; 
      }
   	
      temp = (short)total; 
      
      return temp; 
   }

   short getAddressingMode(short instruction){
      int total;
      total = instruction; 
   
      total = total << 4;
      total = (short)total;
      
      total = total >> 15;
      instruction = (short)total; 
      
      if(instruction == -1){
         instruction = 1;
      }
      
      return instruction;
   }

   short getRegister (short instruction){
      short temp = 0; 
      int total =0;
      
      temp = instruction; 
   
      total = temp << 5;
      temp = (short)total;
      total = temp >> 13;
      temp = (short)total;
      
      return temp;
   
   }

   short getAddress(short instruction){
      short temp; 
      int total; 
   
      temp = instruction; 
   
      total = temp << 8;
      temp = (short)total; 
      total = temp >> 8;
   
      temp = (short)total;
      
      return temp;
   }
   
   void conditionCodes(short value, short tempAddress){
      short address;
      address = mainMemory[tempAddress][0];
      
      if(value == address){
         conditionCode = 0;
      }
      else if(value > address){
         conditionCode = 2;
      }
      else if(value < address){
         conditionCode = 1;
      }
   
   }
   
   short directAddressing(short address){
      int checker = 0; 		
      checker = address; 
      address = mainMemory[checker][0];
   
      return address; 
   }
   short indexedAddressing(short address, short regTemp){
      int checker = 0; 		
      int checkerTwo = 0;
      int sum;
      
      checker = mainMemory[address][0];
      checkerTwo = registers[regTemp];
      sum = checker + checkerTwo;
      
      address = (short)sum; 
      
      return address; 
   }
   
   void dataUnlocks(User cur){
      //unlocks everything with the users 
      //number lock or it leaves it alone
      int lcv;
      short tempData;
      short lock;
      lock = cur.getLockNumber();
      
      for(lcv = 0; lcv < 256; lcv++){
         tempData = mainMemory[lcv][1];
            
         if(tempData == lock){
            mainMemory[lcv][1] = 0;
         }
            
      }//goes through all of memory 
      
   }//data locks
   
   boolean dataLocksChecker(User cur, int line){
      //checks to see if it is lock with users 
      //number if unlocked locks it
      //checks: address locks both if nessassary
   
      short tempLock;
      short lock;
      
      lock = cur.getLockNumber();
      tempLock = mainMemory[line][1];
      
      //System.out.printf("\nADDRESS:%d\t:LOCK:%d",line, tempLock); 
      
      if(tempLock == 0){
         mainMemory[line][1] = lock;
         return false; 
      }
      else if(tempLock == lock){
         return false;
      }
      else{
         return true; 
      }
      
   }
   
}

class User{
   
   private String name;
   private short lockNumber;
   private int waitNumber; //see how long it has been waiting 
   private boolean OS;
   private boolean stop;
   private boolean running; //helps schedualer know it is currently running
   String inputFile;
   short programCounter;
   short startLine; 
   short endLine;
   short regSaved [];
   
   //OS
   User(String inputName, boolean inputOS){
   
      name = inputName;
      OS = inputOS;
      stop = false;
      inputFile = null;
      running = false; 
      waitNumber = -1;
   }
   
   //User
   User(String inputName, boolean inputOS, String inputF, short inputNum){
   
      name = inputName;
      OS = inputOS;
      stop = false;
      inputFile = inputF;
      lockNumber = inputNum;
      running = false; 
      waitNumber = 0;
      regSaved = new short [4];
      
      for(int lcv = 0; lcv < 4; lcv++){
         regSaved [lcv] = 0; 
      }
      
   }
   
   void reset(){
      programCounter = startLine;
      running = false; 
      waitNumber = 0;
      
      for(int lcv = 0; lcv < 4; lcv++){
         regSaved [lcv] = 0; 
      }
   }
      
   boolean getOS(){
      return OS;
   }
   
   String getName(){
      return name;
   }
      
   void setStop(boolean inputStop){
      stop = inputStop;
   }
   
   boolean getStop(){
      return stop;
   }
   
   void setRunning(boolean inputRun){
      running = inputRun;
   }
   
   boolean getRunning(){
      return running;
   }
   
   void setStartLine(short inputSL){
      startLine = inputSL;
      programCounter = startLine; 
   }
   
   short getStartLine(){
      return startLine; 
   }
   
   void setProgramCounter(short inputPC){
      programCounter = inputPC;
   }
   
   short getProgramCounter(){
      return programCounter; 
   }
   
   void setEndLine(short inputLines){
      endLine = inputLines;
   }
   
   short getEndLine(){
      return endLine; 
   }
   
   String getFile(){
      return inputFile; 
   }
   
   short getLockNumber(){
      return lockNumber; 
   }
   
   void setRegSaved(int number, short inputReg){
      regSaved [number] = inputReg;
   }
   
   short getRegSaved(int number){
      return regSaved [number]; 
   }
   
   void setWaitNumber(int inputWN){
      waitNumber = inputWN;
   }
   
   int getWaitNumber(){
      return waitNumber; 
   }
   
}
