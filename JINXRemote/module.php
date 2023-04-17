<?
    // Klassendefinition
    class JINXRemote extends IPSModule 
    { 
	// Überschreibt die interne IPS_Create($id) Funktion
        public function Create() 
        {
            	// Diese Zeile nicht löschen.
            	parent::Create();
 	    	$this->RegisterPropertyBoolean("Open", false);
		$this->ConnectParent("{B1E43BF6-770A-4FD7-B4FE-6D265F93746B}");
 	    	$this->RegisterPropertyInteger("DMXStartChannel", 1);
        }
 	
	public function GetConfigurationForm() 
	{ 
		$arrayStatus = array(); 
		$arrayStatus[] = array("code" => 101, "icon" => "inactive", "caption" => "Instanz wird erstellt"); 
		$arrayStatus[] = array("code" => 102, "icon" => "active", "caption" => "Instanz ist aktiv");
		$arrayStatus[] = array("code" => 104, "icon" => "inactive", "caption" => "Instanz ist inaktiv");
				
		$arrayElements = array(); 
		$arrayElements[] = array("name" => "Open", "type" => "CheckBox",  "caption" => "Aktiv"); 
 		$arrayElements[] = array("type" => "NumberSpinner", "name" => "DMXStartChannel",  "caption" => "DMX-Start-Kanal");
		$arrayElements[] = array("type" => "Label", "label" => "Dieses Gerät benötigt 7 DMX-Kanäle");
		
		$arrayActions = array();
		$arrayActions[] = array("type" => "Label", "label" => "Test Center"); 
		$arrayActions[] = array("type" => "TestCenter", "name" => "TestCenter");
		
 		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements, "actions" => $arrayActions)); 		 
 	}       
	   
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() 
        {
            	// Diese Zeile nicht löschen
            	parent::ApplyChanges();
		
		// Profil anlegen
		$this->RegisterProfileInteger("Jinx.CrossfadeMode", "Gear", "", "", 0, 231, 0);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 0, "Progressive", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 21, "Linear", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 42, "Left Shape", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 63, "Right Shape", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 84, "Left Intensity", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 105, "Right Intensity", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 126, "Left Overlay", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 147, "Right Overlay", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 168, "Left Overlay Border", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 189, "Right Overlay Border", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 210, "Move left/right", "Gear", -1);
		IPS_SetVariableProfileAssociation("Jinx.CrossfadeMode", 231, "Move up/down", "Gear", -1);
		
		// Statusvariablen
		$this->RegisterVariableInteger("CrossfadeMode", "Crossfade Mode", "Jinx.CrossfadeMode", 40);
		$this->EnableAction("CrossfadeMode");
		
		$this->RegisterVariableInteger("Cross", "Cross", "~Intensity.255", 50);
		$this->EnableAction("Cross");
		
		$this->RegisterVariableBoolean("StrobeState", "Strobe Status", "~Switch", 60);
		$this->EnableAction("StrobeState");
		
		$this->RegisterVariableInteger("Strobe", "Strobe", "~Intensity.255", 70);
		$this->EnableAction("Strobe");
		
		$this->RegisterVariableInteger("Master", "Master", "~Intensity.255", 80);
		$this->EnableAction("Master");
		
		/*
		$this->RegisterProfileInteger("IPS2DMX.FM900Reset", "Clock", "", "", 0, 6, 1);
		IPS_SetVariableProfileAssociation("IPS2DMX.FM900Reset", 0, "Aus", "Clock", -1);
		IPS_SetVariableProfileAssociation("IPS2DMX.FM900Reset", 1, "10 sek", "Clock", -1);
		IPS_SetVariableProfileAssociation("IPS2DMX.FM900Reset", 2, "20 sek", "Clock", -1);
		IPS_SetVariableProfileAssociation("IPS2DMX.FM900Reset", 3, "30 sek", "Clock", -1);
		IPS_SetVariableProfileAssociation("IPS2DMX.FM900Reset", 4, "40 sek", "Clock", -1);
		IPS_SetVariableProfileAssociation("IPS2DMX.FM900Reset", 5, "50 sek", "Clock", -1);
		IPS_SetVariableProfileAssociation("IPS2DMX.FM900Reset", 6, "60 sek", "Clock", -1);
		
		$this->RegisterVariableBoolean("Status", "Status", "~Switch", 10);
		$this->EnableAction("Status");
		IPS_SetHidden($this->GetIDForIdent("Status"), false);
		
		$this->RegisterVariableInteger("AutoReset", "Auto Reset", "IPS2DMX.FM900Reset", 20);
		$this->EnableAction("AutoReset");
		IPS_SetHidden($this->GetIDForIdent("AutoReset"), false);
		*/
		
		If ($this->HasActiveParent() == true) {	
			If ($this->ReadPropertyBoolean("Open") == true) {
				If ($this->GetStatus() <> 102) {
					$this->SetStatus(102);
				}
			}
			else {
				If ($this->GetStatus() <> 104) {
					$this->SetStatus(104);
				}
			}
		}
	}
	
	public function RequestAction($Ident, $Value) 
	{
		switch($Ident) {
		case "CrossfadeMode":
			$this->SetCrossfadeMode($Value);
			break;
		case "Cross":
			$this->SetCrossFader($Value);
			break;
		case "StrobeState":
			$this->SetStrobeState($Value);
			break;
		case "Strobe":
			$this->SetStrobeFader($Value);
			break;
		case "Master":
			$this->SetMasterFader($Value);
			break;
		default:
		    throw new Exception("Invalid Ident");
		}
	}
	    
	// Beginn der Funktionen
	public function SetCrossfadeMode(Int $Value)
	{ 
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("SetCrossfadeMode", "Ausfuehrung", 0);
			$CrossfadeModeChannel = 4; //$this->ReadPropertyInteger("DMXStartChannel");
			$this->SendDataToParent(json_encode(Array("DataID"=> "{F241DA6A-A8BD-484B-A4EA-CC2FA8D83031}", "Size" => 1,  "Channel" => $CrossfadeModeChannel, "Value" => $Value, "FadingSeconds" => 0.0, "DelayedSeconds" => 0.0 )));	
			$this->SetValue("CrossfadeMode", $Value);
		}
	} 
	    
	public function SetCrossFader(Int $Value)
	{ 
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("SetCrossFader", "Ausfuehrung", 0);
			$CrossFaderChannel = 5; //$this->ReadPropertyInteger("DMXStartChannel");
			$this->SendDataToParent(json_encode(Array("DataID"=> "{F241DA6A-A8BD-484B-A4EA-CC2FA8D83031}", "Size" => 1,  "Channel" => $CrossFaderChannel, "Value" => $Value, "FadingSeconds" => 0.0, "DelayedSeconds" => 0.0 )));	
			$this->SetValue("Cross", $Value);
		}
	} 
	
	public function SetStrobeState(Bool $Value)
	{ 
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("SetStrobeState", "Ausfuehrung", 0);
			$StrobeStateChannel = 6; //$this->ReadPropertyInteger("DMXStartChannel");
			$StrobeFader = $this->GetValue("Strobe");
			If ($Value == false) {
				$this->SendDataToParent(json_encode(Array("DataID"=> "{F241DA6A-A8BD-484B-A4EA-CC2FA8D83031}", "Size" => 1,  "Channel" => $StrobeStateChannel, "Value" => 0, "FadingSeconds" => 0.0, "DelayedSeconds" => 0.0 )));
			}
			elseif ($Value == true) {
				$this->SendDataToParent(json_encode(Array("DataID"=> "{F241DA6A-A8BD-484B-A4EA-CC2FA8D83031}", "Size" => 1,  "Channel" => $StrobeStateChannel, "Value" => $StrobeFader, "FadingSeconds" => 0.0, "DelayedSeconds" => 0.0 )));
			}
			$this->SetValue("StrobeState", $Value);
		}
	}        
	    
	public function SetStrobeFader(Int $Value)
	{ 
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("SetStrobeFader", "Ausfuehrung", 0);
			$StrobeFaderChannel = 6; //$this->ReadPropertyInteger("DMXStartChannel");
			$StrobeState = $this->GetValue("StrobeState");
			If ($StrobeState == false) {
				$this->SendDataToParent(json_encode(Array("DataID"=> "{F241DA6A-A8BD-484B-A4EA-CC2FA8D83031}", "Size" => 1,  "Channel" => $StrobeFaderChannel, "Value" => 0, "FadingSeconds" => 0.0, "DelayedSeconds" => 0.0 )));	
			}
			elseif ($StrobeState == true) {
				$this->SendDataToParent(json_encode(Array("DataID"=> "{F241DA6A-A8BD-484B-A4EA-CC2FA8D83031}", "Size" => 1,  "Channel" => $StrobeFaderChannel, "Value" => max($Value, 1), "FadingSeconds" => 0.0, "DelayedSeconds" => 0.0 )));
			}
			$this->SetValue("Strobe", $Value);
		}
	}    
	    
	public function SetMasterFader(Int $Value)
	{ 
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("SetMasterFader", "Ausfuehrung", 0);
			$MasterFaderChannel = 7; //$this->ReadPropertyInteger("DMXStartChannel");
			$this->SendDataToParent(json_encode(Array("DataID"=> "{F241DA6A-A8BD-484B-A4EA-CC2FA8D83031}", "Size" => 1,  "Channel" => $MasterFaderChannel, "Value" => $Value, "FadingSeconds" => 0.0, "DelayedSeconds" => 0.0 )));	
			$this->SetValue("Master", $Value);
		}
	}    
	    
	private function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
	{
	        if (!IPS_VariableProfileExists($Name))
	        {
	            IPS_CreateVariableProfile($Name, 1);
	        }
	        else
	        {
	            $profile = IPS_GetVariableProfile($Name);
	            if ($profile['ProfileType'] != 1)
	                throw new Exception("Variable profile type does not match for profile " . $Name);
	        }
	        IPS_SetVariableProfileIcon($Name, $Icon);
	        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
	        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);    
	}    
}
?>
