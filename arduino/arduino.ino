#include <EtherCard.h>
#include <OneWire.h>
#include <DallasTemperature.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

#define ONE_WIRE_BUS 5
#define FAN_START 33
#define FAN_STOP 30


static byte mymac[] = { 0x74,0x69,0x69,0x2D,0x30,0x31 };
static byte myip[] = { 10,0,0,180 };

byte Ethernet::buffer[500];
BufferFiller bfill;

OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);
LiquidCrystal_I2C lcd(0x27, 16, 2);

byte deg[8] = {
  B01000,
  B10100,
  B01000,
  B00011,
  B00100,
  B00100,
  B00011,
  B00000
};

int temp1=-127, temp2=-127, temp3=-127, t=0;
int fan = 0; 

static word homePage() {
  bfill = ether.tcpOffset();
  bfill.emit_p(PSTR(
    "HTTP/1.0 200 OK\r\n"
    "Content-Type: text/html\r\n"
    "Pragma: no-cache\r\n"
    "\r\n"
    "$D,$D,$D,$D"),
      temp1+100, temp2+100, temp3+100, fan);
  return bfill.position();
}


void setup () {
  ether.begin(sizeof Ethernet::buffer, mymac, SS);
  ether.staticSetup(myip);

  lcd.init();
  lcd.createChar(7, deg);
  lcd.backlight();

  sensors.begin();
  sensors.setResolution(9);

  pinMode(7, OUTPUT);
}

void loop () {
  word len = ether.packetReceive();
  word pos = ether.packetLoop(len);

  if (pos) ether.httpServerReply(homePage());

  if(t==600) {
    t=0;
    sensors.requestTemperatures();
    
    temp1 = sensors.getTempCByIndex(0); 
    temp2 = sensors.getTempCByIndex(1);
    temp3 = sensors.getTempCByIndex(2);

    lcd.clear();
    lcd.print("Dom:");
    lcd.print(temp1);
    lcd.write(7);
    lcd.print("  Zew:");
    lcd.print(temp3);
    lcd.write(7);
    lcd.setCursor(0,1);
    lcd.print("Szklarnia:");
    lcd.print(temp2);
    lcd.write(7);

    if(temp2>=FAN_START && temp2<60 && !fan) fan = 1; 
    if(temp2<=FAN_STOP && temp2>-30 && fan) fan = 0;

    if(fan) digitalWrite(7, HIGH);
    else digitalWrite(7, LOW);

  }

  if(t==400) {
    lcd.clear();
    lcd.print("Wentylator: ");
    if(fan) lcd.print("ON");
    else lcd.print("OFF");
    
    lcd.setCursor(0,1);
    lcd.print("Szklarnia:");
    lcd.print(temp2);
    lcd.write(7);
  }

  t++;
  delay(10);
}
