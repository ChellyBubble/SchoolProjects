import java.net.*;
import java.io.*;
import java.util.Random;

public class EchoServer{
   
   public static int column;
   public static int row;
   private static String messages[] = new String[6];
   private static String ships[] = new String[4];
   private static Gameboard user;
   private static boolean winner;
   private static String target;  
   private static BufferedReader stdIn;
   
   public static void main(String args[]) throws IOException{
      if (args.length != 1){
         System.err.println("Usage: java EchoServer <port number>");
         System.exit(1);
      }
   
      int portNumber = Integer.parseInt(args[0]);
      
      setUp();
      
      //Recieve Mode
      try {
      
         ServerSocket serverSocket = new ServerSocket(portNumber);
         Socket clientSocket = serverSocket.accept();
         BufferedReader in =new BufferedReader(new InputStreamReader(clientSocket.getInputStream()));
         stdIn = new BufferedReader(new InputStreamReader(System.in));
         PrintWriter out = new PrintWriter(clientSocket.getOutputStream(), true);
         
         winner = false;
         boolean  hit = false;
         boolean isReady = false;  
         int sunk;
         int sent; 
         
         String inputLine;
         System.out.printf("\nWaiting for READY...");
         
         
         //START
         while (isReady != true){
            
            inputLine = in.readLine();
            
            if(inputLine.equals("READY")){
               isReady = true;
               System.out.printf("\n\nReceived READY.\n"); 
            }
         
         }//ready loop 
         
         System.out.printf("\nClient makes first move. Waiting for Client...\n\n");
         
         //GAME LOOP
         while(winner != true){
            //String- HIT or MISS/SUNK or WINNER/TARGET
            
            //POINT A
            inputLine = in.readLine();
            
            if(inputLine.equals("TIMEOUT")){
               System.out.printf("\n---%s---", inputLine);
               //System.out.printf("\nMessage Recieved.\n\n");
            }
            else{
               
               System.out.printf("\nMessage Recieved.\n\n");
               
               winner = decodeString(inputLine);
               inputLine = "";
            
               //check to see if player won 
               if(winner != true){
               
                  hit = user.isHit(row, column); 
               
                  if(hit == true){
                     inputLine = messages[2] + "/";
                     sunk = user.isShipSunk();
                     if(sunk != -1){
                        inputLine += messages[4] + " " + ships[sunk-2] + "/";
                     }
                     winner = user.isBattleshipSunk();
                  }//HIT
                  else{
                     inputLine += messages[3] + "/";
                  }//MISS
               
                  //POINT B
                  if(winner == true){
                     inputLine += messages[5];
                     System.out.printf("\nOTHER PLAYER SUNK YOUR BATTLESHIP. YOU LOOSE! GAME OVER.\n\n");
                  }
                  else{
                     user.printBoards();
                  
                     getUserInput();
                     inputLine += target; 
                  
                  }//end if client won
                  
                  /*
                  sent = messageSent(); 
                  
                  if(sent == 6){
                     System.out.printf("\n---UNEXPECTED ERROR--- Message NOT sent.");
                     out.println("TIMEOUT");
                     //System.out.printf("\nMessage sent. Waiting for Client...\n\n");
                     sent = 0;
                  }
                  else{
                  */
                  out.println(inputLine);
                  System.out.printf("\nMessage Sent. Waiting for Client...\n\n");
                     
                  //}//end if sent error 
                  
                  
               }//end if sever won
            }
            
         }//end while loop
         
      }//end of try 
      catch (IOException e){
         System.out.println("Excpetion caught when trying to listen on port " + 
                         portNumber + " or listening for a connection");
         System.out.println(e.getMessage());
      }
   }
   
   private static int messageSent(){
      
      Random randomGenerator = new Random ();
      int failRate = randomGenerator.nextInt(10);
      
      return failRate; 
      
   }
   
   private static void getUserInput(){
      
      
      int size;
      
      System.out.printf("\nEXAMPLE- %s: c 10", messages[1]);
      System.out.printf("\n\t %s: ", messages[1]);
         
      try{
         target = stdIn.readLine();
      } 
      catch(IOException ex){
         target = "???"; 
      }
      
      size = target.length();
      
      if(size > 4 || size < 3){
         System.out.printf("\n\n%s: %s\n%s\n\n","There was not a valid entry.", target, "Please try again.");
         getUserInput();
      }
      else{
         stringToInts(target); 
      }
      
   
   }
   
   private static boolean decodeString(String inStr){
      
      int size; 
      
      //System.out.printf("\nReceeiving MOVE\t"); 
      
      String seperateStr [] = inStr.split("/");
      
      size = seperateStr.length; 
      
      
      if(size == 1){
         //target 
         stringToInts(seperateStr[0]);
      }
      else if(size == 2){
         //HIT or MISS
         System.out.printf("\n%s\n", seperateStr[0]); 
         if(seperateStr[0].equals("HIT")){
            user.setTargetBoard(row, column, 8);
         }
         else{
            user.setTargetBoard(row, column, 9);
         }
         
         if(seperateStr[1].equals(messages[5])){
            System.out.printf("\n%s\n\n", seperateStr[1]);
            return true;  
         }//winner
         else{
            stringToInts(seperateStr[1]);
         }//target
      }
      else if(size == 3){
         //HIT
         System.out.printf("\n%s", seperateStr[0]);
         user.setTargetBoard(row, column, 8);
         //SUNK 
         System.out.printf("\n%s\n", seperateStr[1]);  
         //target 
         stringToInts(seperateStr[2]);
      }
      return false; 
   }
   
   private static void stringToInts(String in){
   
      //System.out.printf("\n%s", in); 
      boolean wrong = false; 
      
      char inputFirst;
      char inputSecond; 
      char inputThird;
       
      int size = in.length();
      
      if(in.charAt(1) != ' '){
         wrong = true; 
      }
      
      if(size == 3){
         inputFirst = in.charAt(0);
         inputSecond = in.charAt(2);
         inputThird = '.';
      }
      else{
         inputFirst = in.charAt(0);
         inputSecond = in.charAt(2);
         inputThird = in.charAt(3);
         if(inputSecond != '1' || inputThird != '0'){
            wrong = true; 
         }
      }
      
      
      //finding which row it is in 
      if(inputFirst == 'a'){
         row = 0;
      }
      else if(inputFirst == 'b'){
         row = 1;
      }
      else if(inputFirst == 'c'){
         row = 2;
      }
      else if(inputFirst == 'd'){
         row = 3;
      }
      else if(inputFirst == 'e'){
         row = 4;
      }
      else if(inputFirst == 'f'){
         row = 5;
      }
      else if(inputFirst == 'g'){
         row = 6;
      }
      else if(inputFirst == 'h'){
         row = 7;
      }
      else if(inputFirst == 'i'){
         row = 8;
      }
      else if(inputFirst == 'j'){
         row = 9;
      }
      else{
         wrong = true;
      }
           
      //finding which column it is in 
      if(inputSecond == '1'){
         if(inputThird == '0'){
            column = 9;
         }//if ten 
         else{
            column = 0;
         }
      }
      else if(inputSecond == '2'){
         column = 1;
      }
      else if(inputSecond == '3'){
         column = 2;
      }
      else if(inputSecond == '4'){
         column = 3;
      }
      else if(inputSecond == '5'){
         column = 4;
      }
      else if(inputSecond == '6'){
         column = 5;
      }
      else if(inputSecond == '7'){
         column = 6;
      }
      else if(inputSecond == '8'){
         column = 7;
      }
      else if(inputSecond == '9'){
         column = 8;
      }
      else{
         wrong = true; 
      }
      
      if(wrong == true){
         row = 0;
         column = 0; 
         System.out.printf("\n\n%s: %s\n%s\n\n","There was not a valid entry.", in, "Please try again.");
         getUserInput();
      }
      
   }//end of convertStringToInt

   public static void setUp(){
   
      //create user 2 
      user = new Gameboard("User 2");
      //user.printBoards();
      
      //messages allowed
      messages[0] = "READY";
      messages[1] = "MOVE";
      messages[2] = "HIT";
      messages[3] = "MISS";
      messages[4] = "YOU SUNK MY";
      messages[5] = "YOU SUNK MY BATTLESHIP. YOU WIN! GAME OVER.";
      
      //ships
      ships[0] = "PATROL";
      ships[1] = "SUBMARINE";
      ships[2] = "DESTROYER";
      ships[3] = "CARRIER";
      
   }
   
   
}
