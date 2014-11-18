--#!/usr/bin/mysql -u root -p --password=xxx roprotokol

DROP DATABASE IF EXISTS roprotokol;

CREATE DATABASE IF NOT EXISTS roprotokol;
use roprotokol;

CREATE TABLE IF NOT EXISTS Tur (
       TurID INT PRIMARY KEY,
       FK_BådID INT NOT NULL,
       Ud DATETIME,
       Ind DATETIME,
       ForvInd DATETIME,
       Destination VARCHAR(100),
       Meter INT,
       FK_TurTypeID INT,
       Kommentar VARCHAR(1000),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer CHAR(10),
       DESTID INT
);

CREATE INDEX turfk on Tur(FK_BådID);
CREATE INDEX turud on Tur(ud);

CREATE TABLE IF NOT EXISTS Trip (
       TripID INT,
       Season INT,
       BoatID INT NOT NULL,
       OutTime DATETIME,
       InTime DATETIME,
       ExpectedIn DATETIME,
       Destination VARCHAR(100),
       Meter INT,
       TripTypeID INT,
       Comment VARCHAR(1000),
       CreatedDate DATE,
       EditDate DATE,
       Initials CHAR(10),
       DESTID INT,
       PRIMARY KEY   (Season,TripID)
);

CREATE INDEX  tripfk on Trip(BoatID);
CREATE INDEX tripout on Trip(OutTime);


CREATE TABLE IF NOT EXISTS Båd (
    BådID INT PRIMARY KEY,
    Navn VARCHAR(100) NOT NULL, -- FIXME should be unique: Balder
    FK_GruppeID INT,
    Pladser INT,
    Beskrivelse VARCHAR(100),
    OprettetDato DATETIME,
    RedigeretDato DATETIME,
    Initialer CHAR(10),
    MotionPlus VARCHAR(100),
    Type VARCHAR(100), -- FIXME was TYPE
    Anvendelse VARCHAR(100),
    Niveau VARCHAR(100)

);

CREATE TABLE IF NOT EXISTS Bådindstilling (
    BådID Int, -- FIXME should be Unique
    Navn VARCHAR(100) NOT NULL,
    Plads Int,
    Åretype VARCHAR(100),
    Righøjde Numeric(8,2),
    Svirvelafstand Numeric(8,2),
    Svirveltype VARCHAR(100),
    Åresmig Numeric(8,2),
    Stammevinkel Numeric(8,2),
    Årelængde Numeric(8,2),
    ÅrelængdeIndvendig Numeric(8,2),
    Håndtagslængde Numeric(8,2),
    Sædetype VARCHAR(100),
    Skinnelængde Numeric(8,2),
    SkinneForanSæde Numeric(8,2),
    Bensparksdybde Numeric(8,2),
    Sparkevinkel Numeric(8,2),
    Spændholttype VARCHAR(100),
    Omsætningsforhold Numeric(8,2),
    Gearingsforhold Numeric(8,2),
    ØnsketOmsætningsforhold Numeric(8,2),
    ØnsketGearingsforhold Numeric(8,2),
    NyÅrelængde Numeric(8,2),
    NyIndvendiglængde Numeric(8,2),
    OprettetDato DATETIME,
    RedigeretDato DATETIME,
    Kommentar VARCHAR(1000),
    Initialer CHAR(10)
);


CREATE TABLE IF NOT EXISTS BådKategori (
       BådKategoriID INT PRIMARY KEY,
       Navn VARCHAR(100) UNIQUE NOT NULL,
       Beskrivelse VARCHAR(1000),
       OprettetDato DATETIME,
       RedigeretDato DATETIME,
       Initialer CHAR(10)
);

CREATE TABLE IF NOT EXISTS Fejl_system (
       FejlID INT PRIMARY KEY,
       Indberetter VARCHAR(100),
       Beskrivelse VARCHAR(1000),
       Dato DATETIME,
       Mail VARCHAR(300)
);


CREATE TABLE IF NOT EXISTS Fejl_tblMembersSportData (
       FejlID INT PRIMARY KEY,
       Navn VARCHAR(100),
       MemberID INT,
       Roret INT,
       TeoretiskStyrmandKursus INT,
       Styrmand INT,
       Langtur INT,
       Ormen INT,
       Svava INT,
       Sculler INT,
       Kajak INT,
       Kajak_2 INT,
       RoInstruktoer INT,
       StyrmandInstruktoer INT,
       ScullerInstruktoer INT,
       KajakInstruktoer INT,
       Kaproer INT,
       Motorboat INT,
       Indberetter VARCHAR(100),
       Mail VARCHAR(300),
       Kommentar VARCHAR(1000),
       Fixed_Comment VARCHAR(1000),
       Fixed INT
);

CREATE TABLE IF NOT EXISTS  Fejl_tur (
       FejlID INT PRIMARY KEY,
       SletTur INT,
       TurID INT,
       Season INT,
       Båd VARCHAR(100),
       Ud DATETIME,
       Ind DATETIME,
       Destination VARCHAR(100),
       Distance INT,
       TurType VARCHAR(100),
       TurDeltager0 VARCHAR(100),
       TurDeltager1 VARCHAR(100),
       TurDeltager2 VARCHAR(100),
       TurDeltager3 VARCHAR(100),
       TurDeltager4 VARCHAR(100),
       TurDeltager5 VARCHAR(100),
       TurDeltager6 VARCHAR(100),
       TurDeltager7 VARCHAR(100),
       TurDeltager8 VARCHAR(100),
       TurDeltager9 VARCHAR(100),
       Årsagtilrettelsen VARCHAR(1000), -- RM space
       Indberetter VARCHAR(100),
       Mail VARCHAR(300),
       Fixed_comment VARCHAR(1000),
       Fixed INT 
);

CREATE TABLE IF NOT EXISTS Gruppe (
       GruppeID INT PRIMARY KEY,
       GruppeNr INT UNIQUE NOT NULL,
       Navn VARCHAR(100),
       Pladser INT,
       Beskrivelse VARCHAR(1000),
       FK_BådKategoriID INT,
       OprettetDato DATETIME,
       RedigeretDato DATETIME,
       Initialer CHAR(10)
);
CREATE INDEX gruppenavn on Gruppe(Navn);


CREATE TABLE IF NOT EXISTS  Kajak_typer (
       ID INT PRIMARY KEY,
       Typenavn CHAR(100) UNIQUE NOT NULL
);


CREATE TABLE IF NOT EXISTS Kommentar (
    Art VARCHAR(100),
    FK_ID INT,
    Dato DATE,
    Tid TIME,
    Kommentar VARCHAR(100)
);


CREATE TABLE IF NOT EXISTS LåsteBåde (
       BoatID INT PRIMARY KEY,
       KlientNavn VARCHAR(100),
       locktimeout INT -- type guessed
);

CREATE TABLE IF NOT EXISTS  Medlem (
       MedlemID INT PRIMARY KEY,
       Medlemsnr CHAR(10) NOT NULL,  -- FIXME UNIQUE: 4419 Frederik Thuesen
       Fornavn VARCHAR(100),
       Efternavn VARCHAR(100),
       Adresse VARCHAR(100),
       FK_Postnr INT,
       Telefon1 CHAR(20),
       Telefon2 CHAR(20),
       Fødselsdag DATETIME,
       Password VARCHAR(100),
       Aktiv INT,
       Rettigheder VARCHAR(100),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer CHAR(10)
);

CREATE TABLE IF NOT EXISTS  Motionstatus ( -- FIXME was motion+status
       MotionstatusID INT PRIMARY KEY,
       Motionstatus VARCHAR(100)

);

CREATE TABLE IF NOT EXISTS  Postnr (
       Postnr INT PRIMARY KEY,
       Distrikt CHAR(100) 
);

CREATE TABLE IF NOT EXISTS Reservation (
       ID INT PRIMARY KEY,
       FK_BådID INT,
       Start DATETIME,
       Slut DATETIME,
       FK_MedlemID INT,
       Beskrivelse VARCHAR(1000),
       FK_SlettetAf INT,
       Formål VARCHAR(100),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer CHAR(10)
);

CREATE TABLE IF NOT EXISTS Skade (
       SkadeID INT PRIMARY KEY,
       FK_BådID INT NOT NULL,
       FK_Ansvarlig INT,
       Ødelagt DATETIME,
       FK_Reperatør INT,
       Grad INT,
       Repareret DATETIME,
       Beskrivelse VARCHAR(1000),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer CHAR(10)
);

CREATE TABLE IF NOT EXISTS TurDeltager (
       FK_TurID INT,
       Plads INT,
       FK_MedlemID INT,
       Navn VARCHAR(100),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer CHAR(10),
       PRIMARY KEY(FK_TurID,Plads)
);

CREATE TABLE IF NOT EXISTS TripMember (
       TripID INT,
       Season INT,
       Seat INT,
       MemberID INT,
       MemberName VARCHAR(100),
       CreatedDate DATE,
       EditDate DATE,
       Initials CHAR(10),
       PRIMARY KEY(TripID,Season,Seat)
);


CREATE TABLE IF NOT EXISTS TurType (
       TurTypeID INT PRIMARY KEY,
       Navn VARCHAR(100) UNIQUE NOT NULL,	
       Beskrivelse VARCHAR(1000),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer CHAR(10),
       Aktiv INT
);

CREATE TABLE IF NOT EXISTS Vintervedligehold (
       Id INT PRIMARY KEY,
       Medlemsnr CHAR(8),
       Season INT,
       HasRedKey INT,
       DeletedReason VARCHAR(100)
);
CREATE INDEX vintermedlem on Vintervedligehold(Medlemsnr);


CREATE TABLE IF NOT EXISTS Destination (
       DestID INT  PRIMARY KEY,
       Navn VARCHAR(100) UNIQUE NOT NULL,
       Meter INT,
       Beskrivelse VARCHAR(1000),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer CHAR(10),
       Gennemsnitlig_varighed_Normal NUMERIC(8,2),
       Gennemsnitlig_varighed_Instruktion NUMERIC(8,2)
);


CREATE TABLE IF NOT EXISTS Kajak_anvendelser (
       ID INT PRIMARY KEY,
       Anvendelse VARCHAR(100) UNIQUE NOT NULL,
       Beskrivelse VARCHAR(1000)
);

CREATE TABLE tblMembers (
  MemberID         INT PRIMARY KEY, 
  LastName         VARCHAR(100), 
  FirstName        VARCHAR(100), 
  Birthdate        DATETIME, 
  Sex              CHAR(2), 
  Address1         CHAR(54), 
  Address2         CHAR(54), 
  Postnr           CHAR(8), 
  City             CHAR(40), 
  Telephone1       CHAR(40), 
  Telephone2       CHAR(40), 
  Fax              CHAR(40), 
  E_mail           VARCHAR(500), 
  MemberType       Int, 
  Diverse1         VARCHAR (100), 
  Diverse2         VARCHAR (140), 
  DiverseMemo      VARCHAR(255), 
  JoinDate	   DATETIME, 
  Control          Int, 
  OldBalance      Double, 
  Subscription      Double, 
  RefusedPayed      Double, 
  Surcharge      Double, 
  ExtraCharge      Double, 
  ExtraChargeText      Text (100), 
  AddSubscription      Boolean NOT NULL, 
  SendAbroad      Boolean NOT NULL, 
  SendInvoice      Boolean NOT NULL, 
  SendInvoiceExtraordinary      Boolean NOT NULL, 
  ReminderTextSurcharge      Boolean NOT NULL, 
  JoinJournalDate      DateTime, 
  RemoveDate      DateTime, 
  RemoveJournaLDate      DateTime, 
  SleepTo      DateTime, 
  InvoiceText1      Text (120), 
  InvoiceText2      Text (120), 
  InvoiceText3      Text (120), 
  InvoiceText4      Text (120), 
  InvoiceText5      Text (120), 
  InvoiceText6      Text (120), 
  E_mailText1      Text (300), 
  EraseTextNext      Boolean NOT NULL, 
  NewsletterStart      Boolean NOT NULL, 
  NewsletterStop      Boolean NOT NULL, 
  NewsletterChange      Boolean NOT NULL, 
  NewsletterReceives      Boolean NOT NULL, 
  E_mail_News      Boolean NOT NULL, 
  OnAddressList      Boolean NOT NULL, 
  OnTelList      Boolean NOT NULL, 
  Danish      Boolean NOT NULL, 
  CprNo      Boolean NOT NULL, 
  Marker      Boolean NOT NULL, 
  Parent      INT, 
  Exam      Boolean NOT NULL, 
  Has_Joined      Boolean NOT NULL
);

CREATE TABLE tblRowClubs (
  MemberID      INT, 
  Name      VARCHAR(500), 
  Address1    VARCHAR(500), 
  Address2    VARCHAR(500), 
  Postnr      CHAR (8), 
  City      CHAR (30), 
  NewsletterReceives  Boolean NOT NULL, 
  Date      CHAR (100), 
  Misc      CHAR (100)
);

