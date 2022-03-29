<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Fattura;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BillsController extends Controller
{
    public function index(){

        return view('welcome');
    }


    public function upload(Request $request)
    {
        // uploaded file saved on local disk
        $data = $request->all();
        $zipFile = $data['uploadFile'];
        $zipName = $zipFile->getClientOriginalName();
        Storage::putFileAs('fatture', $zipFile, $zipName);

        $this->extractFile($zipName);
        $dataArray = $this->readDirectory();
        $fattureArray = $this->readFile($dataArray);
        // dd($fattureArray);

        foreach ($fattureArray as $fattura){
            $newFattura = new Fattura();
            $newFattura->NumeroFattura = Arr::get($fattura, 'FatturaElettronicaHeader.DatiTrasmissione.IdTrasmittente.IdCodice');
            $newFattura->RagSocDest = Arr::get($fattura, 'FatturaElettronicaHeader.CessionarioCommittente.DatiAnagrafici.Anagrafica.Denominazione');
            $newFattura->PIvaDest = Arr::get($fattura, 'FatturaElettronicaHeader.CessionarioCommittente.DatiAnagrafici.IdFiscaleIVA.IdCodice');
            $newFattura->TipoFattura = Arr::get($fattura, 'FatturaElettronicaBody.DatiGenerali.DatiGeneraliDocumento.TipoDocumento');
            $newFattura->DataFattura = Arr::get($fattura, 'FatturaElettronicaBody.DatiGenerali.DatiGeneraliDocumento.Data');
            $newFattura->Importo = Arr::get($fattura, 'FatturaElettronicaBody.DatiBeniServizi.DatiRiepilogo.ImponibileImporto');
            $newFattura->imposta = Arr::get($fattura, 'FatturaElettronicaBody.DatiBeniServizi.DatiRiepilogo.Imposta');
            $newFattura->EsigIva = Arr::get($fattura, 'FatturaElettronicaBody.DatiBeniServizi.DatiRiepilogo.EsigibilitaIVA');
            $newFattura->save();
        }

        // dd($dataArray);

        return view('success');
    }


    // Simple Function that extracts the content of the zip file on the local disk
    private function extractFile($zipName)
    {
        $zip = new ZipArchive();
        $path = storage_path(). "/app/fatture/$zipName";

        if($zip->open($path) === true) {
            $zip->extractTo(storage_path(). "/app/fatture/myfiles");
            $zip->close();
        };
    }


    //this Function is responsible for reading the single .xml file to get the information out
    private function readFile($dataArray)
    {
        $arrayFinal = [];
        foreach($dataArray as $url){
            $xmlString = file_get_contents($url);
            $xmlObject = simplexml_load_string($xmlString);

            $json = json_encode($xmlObject);
            $array = json_decode($json, true);

            array_push($arrayFinal, $array);
        }
        return $arrayFinal;
    }


    // this Function read the directory where the zip file was extracted to and checks for duplicates, returning an array
    private function readDirectory()
    {
        $path = storage_path(). "/app/fatture/myfiles";

        //get all files Urls from Directory structure
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $filesArray = [];

        foreach ($files as $file){
            if(!$file->isDir()){
                $filesArray[] = $file->getPathname();
            }
        }

        // this code creates file Hashes for each file
        $length = count($filesArray);
        $hashes = [];

        for($i=0; $i<$length; $i++){
            $hashes[$i] = hash_file("sha256", $filesArray[$i]);
        }

        // remove the duplicates files
        $newArray = [];
        $indexes = [];
        for($i=0; $i<$length; $i++){
            if(!in_array($hashes[$i], $newArray)){
                array_push($newArray, $hashes[$i]);
                array_push($indexes, $i);
            }
        }

        // retrive original files
        $finalArray = [];
        foreach($indexes as $index){
            array_push($finalArray, $filesArray[$index]);
        }

        return $finalArray;
    }
}
