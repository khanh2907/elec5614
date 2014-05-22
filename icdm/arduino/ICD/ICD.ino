// DigiX WiFi module example - released by Digistump LLC/Erik Kettenburg under CC-BY-SA 3.0
// Inspired by HttpClient library by MCQN Ltd.

#include <DigiFi.h>
#include <ChibiOS_ARM.h>

DigiFi wifi;


float *heartrate;
int electrode = 13;
static Mutex mtx;
int emergency = 0;

void setup()
{
  chMtxInit(&mtx);
  pinMode(electrode, OUTPUT);
  digitalWrite(electrode, LOW);
  
  // initialize serial communications at 9600 bps:
  Serial.begin(9600); 
  wifi.begin(9600);

  //DigiX trick - since we are on serial over USB wait for character to be entered in serial terminal
  while(!Serial.available()){
    Serial.println("Enter any key to begin");
    delay(1000);
  }

  Serial.println("Starting");

  while (wifi.ready() != 1)
  {
    Serial.println("Error connecting to network");
    delay(15000);
  }  
  
  Serial.println("Connected to wifi!");
  
//GET request example

  if(wifi.get("130.56.248.71","/")){
    String body = wifi.body();
    Serial.println(body);
  }
  else{
    Serial.println("error");
  
  }
  
  //POST request example
Serial.println("Sending post!");
 //To use thingspeak for sending tweets see: http://community.thingspeak.com/documentation/apps/thingtweet/
  if(wifi.post("130.56.248.71","/elec5614/","name=Khanh")){
    String body = wifi.body();
    Serial.println(body);
  }
  else{
    Serial.println("error");
  
  }
  
  chBegin(chSetup);
  while(1){}
  
  wifi.close();
}

void chSetup(){
   //create monitoring thread + send thread
  
  Thread *tp1 = chThdCreateFromHeap(NULL, THD_WA_SIZE(128), NORMALPRIO+1, monHeart, (void *) heartrate);
  Thread *tp2 = chThdCreateFromHeap(NULL, THD_WA_SIZE(128), NORMALPRIO+1, sendHeart, (void *) heartrate);
  Thread *tp3 = chThdCreateFromHeap(NULL, THD_WA_SIZE(128), NORMALPRIO+1, stimulate, NULL);
}
static msg_t monHeart(void *arg){
    while(1){
  if(Serial.available()>0){
      *heartrate = Serial.parseFloat();
      
      if(*heartrate<60 || *heartrate > 120){
         emergency = 1; 
      }
      }
    return 0;
    }
}

static msg_t stimulate(void *arg){
  while(1){
    if(emergency){
     chThdSleepMilliseconds(5000);
     digitalWrite(electrode, HIGH);
     digitalWrite(electrode, LOW);
     emergency = 0;
    }
  }
}

//Thread to 
static msg_t sendHeart(void *arg){
  while(1){}
}

//Not used
void loop()
{
  

}
