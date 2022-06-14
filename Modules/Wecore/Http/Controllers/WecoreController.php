<?php

namespace Modules\Wecore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Wecore\Entities\Core;
use Modules\Wecore\Http\Requests\CreateCoreRequest;
use Modules\Wecore\Http\Requests\UpdateCoreRequest;
use Modules\Wecore\Repositories\CoreRepository;
use Db;

use Spatie\Activitylog\Models\Activity;
use Modules\Tasklist\Entities\RinnovoNotifica;
use Modules\Commerciale\Entities\FatturazioneScadenze;
use Modules\Commerciale\Entities\Offerte;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Tasklist\Entities\Attivita;

class WecoreController  extends  BasePublicController
{
  /**
   * @var CoreRepository
   */

  public function __construct(CoreRepository $core)
  {
    parent::__construct();
  }

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function rubricaWecom(Request $request)
  {
    $aziende_ip = json_decode(setting('wecore::aziende_ip'));
    $ip =  $request->ip();
    $allow = false;
    foreach ($aziende_ip  as $key => $value) {
      if ($value == $ip) {
        $allow = true;
      }
    }



    if ($allow) {

      //$cores = $this->core->all();
      $query = Db::select("SELECT r.id,
trim(r.ragione_sociale) AS Name,
REPLACE ( REPLACE ( i.telefono, ' ', '' ),  '-',  '' ) AS Telephone , '' as num_telefono_aziendale
FROM
amministrazione__clienti_indirizzi i
LEFT JOIN amministrazione__clienti r ON r.id = i.cliente_id 
WHERE
trim(REPLACE ( i.telefono, '0', '' )) <> '' 
and trim(REPLACE ( REPLACE ( r.ragione_sociale, '.', '' ), '-', '' ))   <> '' 

UNION

SELECT i.id,
trim(ifnull(concat_ws( ' ', i.nome, i.cognome ), r.ragione_sociale )) AS nominativo,
REPLACE (
  REPLACE ( i.telefono, ' ', '' ),
  '-',
  '' 
) AS numero , '' as num_telefono_aziendale

FROM
amministrazione__clienti_referenti i
LEFT JOIN amministrazione__clienti r ON r.id = i.cliente_id 
WHERE
trim(REPLACE ( i.telefono, '0', '' )) <> ''
and trim(REPLACE(REPLACE(ifnull(concat_ws( ' ', i.nome, i.cognome ), r.ragione_sociale ), '.', '' ), '-', '' )) <> '' 

 UNION
SELECT u.id,
trim(concat_ws( ' ', u.first_name, u.last_name )) AS nominativo,
i.interno AS numero  ,
i.num_telefono_aziendale as num_telefono_aziendale
FROM
profile__profiles i
LEFT JOIN users u ON u.id = i.user_id 
WHERE
i.interno > 100 

ORDER BY
Name");


      $Rubrica = new \SimpleXMLElement('<WeComIPPhoneDirectory></WeComIPPhoneDirectory>');
      foreach ($query as $key => $value) {
        if (trim(str_replace('.', '', $value->Telephone)) != '') {
	  $nome=htmlspecialchars($value->Name);
	  $id_nome=$value->id.'|'.$nome;
          $dir =  $Rubrica->addChild('DirectoryEntry');
	  	  $dir->addChild('IDName', substr($id_nome,0,61));
          $dir->addChild('Name', substr($nome,0,61));
          $dir->addChild('Telephone', $value->Telephone);
		  $dir->addChild('num_telefono_aziendale', $value->num_telefono_aziendale);
        }
      }
      $headers = [
        'Content-Type' => 'application/xml'
      ];
      ob_clean();
      return response($Rubrica->asXML(), 200, $headers);
    } else {
      return '';
    }
  }
}