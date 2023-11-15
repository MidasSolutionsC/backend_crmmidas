<?php
namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Contact;
use App\Models\IdentificationDocument;
use App\Models\TmpSale;
use App\Services\Implementation\BankAccountService;
use Illuminate\Http\Request;
use App\Services\Implementation\ClientService;
use App\Services\Implementation\CompanyService;
use App\Services\Implementation\ContactService;
use App\Services\Implementation\IdentificationDocumentService;
use App\Services\Implementation\PersonService;
use App\Validator\BankAccountValidator;
use App\Validator\ClientValidator;
use App\Validator\CompanyValidator;
use App\Validator\ContactValidator;
use App\Validator\IdentificationDocumentValidator;
use App\Validator\PersonValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller{

  private $request;
  private $clientService;
  private $clientValidator;

  private $personService;
  private $personValidator;

  private $companyService;
  private $companyValidator;

  private $identificationService;
  private $identificationValidator;

  private $contactService;
  private $contactValidator;

  private $bankAccountService;
  private $bankAccountValidator;

  public function __construct(
    Request $request, 
    ClientService $clientService, 
    ClientValidator $clientValidator,
    PersonService $personService,
    PersonValidator $personValidator,
    CompanyService $companyService,
    CompanyValidator $companyValidator,
    IdentificationDocumentService $identificationService,
    IdentificationDocumentValidator $identificationValidator,
    ContactService $contactService,
    ContactValidator $contactValidator,
    BankAccountService $bankAccountService,
    BankAccountValidator $bankAccountValidator
    ) {
    $this->request = $request;
    $this->clientService = $clientService;
    $this->clientValidator = $clientValidator;
    $this->personService = $personService;
    $this->personValidator = $personValidator;
    $this->companyService = $companyService;
    $this->companyValidator = $companyValidator;
    $this->identificationService = $identificationService;
    $this->identificationValidator = $identificationValidator;
    $this->contactService = $contactService;
    $this->contactValidator = $contactValidator;
    $this->bankAccountService = $bankAccountService;
    $this->bankAccountValidator = $bankAccountValidator;
  }

  public function listAll(){
    try{
      $result = $this->clientService->getAll();
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los clientes', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->clientService->getById($id);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function getByPersonId($personId){
    try{
      $result = $this->clientService->getByPersonId($personId);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function getByCompanyId($companyId){
    try{
      $result = $this->clientService->getByCompanyId($companyId);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->clientService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->clientService->update($this->request->all(), $id);
        if(!is_null($result)){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del cliente', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){
    try{
      // Iniciar una transacción
      DB::beginTransaction();
      $resultFull = [];
      $validationError = false; // Variable de control

      $dataPerson = $this->request->input('person');
      $dataCompany = $this->request->input('company');
      $personaJuridica = $this->request->input('persona_juridica');
      $this->request['persona_juridica'] = boolval($personaJuridica);

      $cuentasBancarias = $this->request->input('bank_accounts');

      if(is_null($personaJuridica)) {
        $response = $this->responseError(["persona_juridica" => ["No especifico el campo persona jurídica"]], 422);
        DB::rollBack();
        return $response;
      }

      if(boolval($personaJuridica)){
        // EMPRESA
        $dataClientWithoutPerson = $this->request->except(['personas_id']);

        if(empty($this->request->input('empresas_id'))){
          if(empty($dataCompany['codigo_ubigeo'])){
            unset($dataCompany['codigo_ubigeo']);
          }

          // registrar empresa
          $this->companyValidator->setRequest($dataCompany);
          $validatorCompany = $this->companyValidator->validate();

          if($validatorCompany->fails()){
            $response = $this->responseError($validatorCompany->errors(), 422);
            DB::rollBack();
            return $response;
          } else {
            // registrar empresa
            $dataCompany['user_create_id'] = $this->request->input('user_auth_id');

            if(empty($dataCompany['id'])){
              $resCompany = $this->companyService->create($dataCompany);
            } else {
              $resCompany = $this->companyService->update($dataCompany, $dataCompany['id']);
            }

            if($resCompany){
              $this->request['empresas_id'] = $resCompany->id;
              $dataClientWithoutPerson['empresas_id'] = $resCompany->id;
              $resultFull['company'] = $resCompany;

              $identificaciones = $dataCompany['identifications'];
              $contactos = $dataCompany['contacts'];

              // REGISTRAR IDENTIFICACIONES DE LA EMPRESA
              foreach($identificaciones as $identity){
                $identity['empresas_id'] = $resCompany->id;
                $identity['user_create_id'] = $this->request->input('user_auth_id');

                $this->identificationValidator->setRequest($identity, $identity['id']);
                $validatorIdentity = $this->identificationValidator->validate();
    
                if ($validatorIdentity->fails()) {
                  $response = $this->responseError($validatorIdentity->errors(), 422);
                  DB::rollBack();
                  return $response;
                  break; // Detener el ciclo en caso de error
                } else {
                  // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
                  if(empty($identity['id'])){
                    $this->identificationService->create($identity);
                  } else {
                    $this->identificationService->update($identity, $identity['id']);
                  }
                }
              }

              // REGISTRAR CONTACTOS
              foreach($contactos as $contact){
                $contact['empresas_id'] = $resCompany->id;
                

                $this->contactValidator->setRequest($contact, $contact['id']);
                $validatorContact = $this->contactValidator->validate();
    
                if ($validatorContact->fails()) {
                  $response = $this->responseError($validatorContact->errors(), 422);
                  DB::rollBack();
                  return $response;
                  break; // Detener el ciclo en caso de error
                } else {
                  // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
                  if(empty($contact['id'])){
                    $contact['user_create_id'] = $this->request->input('user_auth_id');
                    $this->contactService->create($contact);
                  } else {
                    $contact['user_update_id'] = $this->request->input('user_auth_id');
                    $this->contactService->update($contact, $contact['id']);
                  }
                }
              }

              $resCompany->load('identifications', 'contacts', 'addresses');
              $resultFull['company'] = $resCompany;
            }
          }
        } // FIN PROCESO - EMPRESA

        $this->clientValidator->setRequest($dataClientWithoutPerson);
      } else {
        // PERSONA        
        $dataClientWithoutCompany = $this->request->except(['empresas_id']);
    
        if(empty($this->request->input('personas_id'))){

          if(empty($dataPerson['codigo_ubigeo'])){
            unset($dataPerson['codigo_ubigeo']);
          }

          $this->personValidator->setRequest($dataPerson);
          $validatorPerson = $this->personValidator->validate();
    
          if($validatorPerson->fails()){
            $response = $this->responseError($validatorPerson->errors(), 422);
            DB::rollBack();
            return $response;
          } else {
            // REGISTRAR O ACTUALIZAR LOS DATOS DE LA PERSONA
            $resPerson = null;
            if(empty($dataPerson['id'])){
              $resPerson = $this->personService->create($dataPerson);
            } else {
              $resPerson = $this->personService->update($dataPerson, $dataPerson['id']);
            }

            if($resPerson){
              $this->request['personas_id'] = $resPerson->id;
              $dataClientWithoutCompany['personas_id'] = $resPerson->id;

              $identificaciones = $dataPerson['identifications'];
              $contactos = $dataPerson['contacts'];

              // REGISTRAR IDENTIFICACIONES DE LA PERSONA
              foreach($identificaciones as $identity){
                $identity['personas_id'] = $resPerson->id;
                $identity['user_create_id'] = $this->request->input('user_auth_id');
                $this->identificationValidator->setRequest($identity, $identity['id']);
                $validatorIdentity = $this->identificationValidator->validate();
    
                if ($validatorIdentity->fails()) {
                  $response = $this->responseError($validatorIdentity->errors(), 422);
                  DB::rollBack();
                  return $response;
                  break; // Detener el ciclo en caso de error
                } else {
                  // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
                  if(empty($identity['id'])){
                    $this->identificationService->create($identity);
                  } else {
                    $this->identificationService->update($identity, $identity['id']);
                  }
                }
              }

              // REGISTRAR CONTACTOS
              foreach($contactos as $contact){
                $contact['personas_id'] = $resPerson->id;
                $contact['user_create_id'] = $this->request->input('user_auth_id');

                $this->contactValidator->setRequest($contact, $contact['id']);
                $validatorContact = $this->contactValidator->validate();
    
                if ($validatorContact->fails()) {
                  $response = $this->responseError($validatorContact->errors(), 422);
                  DB::rollBack();
                  return $response;
                  break; // Detener el ciclo en caso de error
                } else {
                  // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
                  if(empty($contact['id'])){
                    $this->contactService->create($contact);
                  } else {
                    $this->contactService->update($contact, $contact['id']);
                  }
                }
              }
    
              $resPerson->load('identifications', 'contacts', 'addresses');
              $resultFull['person'] = $resPerson;
            }
          }
        } // FIN PROCESO - PERSONA

        $this->clientValidator->setRequest($dataClientWithoutCompany);
      } 


      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
        DB::rollBack();
        return $response;
      } else {
        // CLIENTE
        $resClient = $this->clientService->create($this->request->all());

        $resultCuentaBancarias = [];
        
        // REGISTRAR CONTACTOS
        foreach($cuentasBancarias as $bankAccount){
          $bankAccount['clientes_id'] = $resClient->id;
          $bankAccount['user_create_id'] = $this->request->input('user_auth_id');

          $this->bankAccountValidator->setRequest($bankAccount, $bankAccount['id']);
          $validatorBankAccount = $this->bankAccountValidator->validate();

          if ($validatorBankAccount->fails()) {
            $response = $this->responseError($validatorBankAccount->errors(), 422);
            DB::rollBack();
            return $response;
            break; // Detener el ciclo en caso de error
          } else {
            // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
            if(empty($bankAccount['id'])){
              $resultCuentaBancarias[] = $this->bankAccountService->create($bankAccount);
            } else {
              $resultCuentaBancarias[] = $this->bankAccountService->update($bankAccount, $bankAccount['id']);
            }
          }
        }

        $resClient->load('bankAccounts');
        $resultFull['client'] = $resClient;

        $response = $this->responseCreated($resultFull);
      }
  
      // Si todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      // Maneja la excepción, por ejemplo, muestra un mensaje de error
      DB::rollBack();
      return $this->responseError(['message' => 'Error al actualizar la venta', 'error' => $e->getMessage()], 422);
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch(\Exception $e){
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al crear el cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function updateComplete($id){
    try{
      // Iniciar una transacción
      DB::beginTransaction();
      $resultFull = [];

      $dataPerson = $this->request->input('person');
      $dataCompany = $this->request->input('company');
      $personaJuridica = $this->request->input('persona_juridica');
      $this->request['persona_juridica'] = boolval($personaJuridica);

      $cuentasBancarias = $this->request->input('bank_accounts');

      if(is_null($personaJuridica)) {
        $response = $this->responseError(["persona_juridica" => ["No especifico el campo persona juridica"]], 422);
        DB::rollBack();
        return $response;
      }

      if(boolval($personaJuridica)){
        $dataClientWithoutPerson = $this->request->except(['personas_id']);

        if(empty($dataCompany['codigo_ubigeo'])){
          unset($dataCompany['codigo_ubigeo']);
        }

        if(!empty($dataCompany)){
          // actualizar empresa
          $this->companyValidator->setRequest($dataCompany, $dataCompany['id']);
          $validatorCompany = $this->companyValidator->validate();

          if($validatorCompany->fails()){
            $response = $this->responseError($validatorCompany->errors(), 422);
            DB::rollBack();
            return $response;
          } else {
            // actualizar empresa
            $dataCompany['user_update_id'] = $this->request->input('user_auth_id');
            $resCompany = $this->companyService->update($dataCompany, $dataCompany['id']);

            if($resCompany){
              $identificaciones = $dataCompany['identifications'];
              $contactos = $dataCompany['contacts'];

              // DOCUMENTOS
              $beforeIdentifications = $resCompany->identifications;
              $beforeIdentifications = $beforeIdentifications->toArray();

              if(count($beforeIdentifications) > 0){
                // Extrae los IDs de los arrays en $identificaciones
                $beforeIds = array_column($beforeIdentifications, 'id');
                $identIds = array_column($identificaciones, 'id');
                $itemsToDelete = array_diff($beforeIds, $identIds);
                          
                // ELIMINAR DOCUMENTOS
                IdentificationDocument::whereIn('id', $itemsToDelete)->delete();
              }


              // CONTACTOS
              $beforeContacts = $resCompany->contacts;
              $beforeContacts = $beforeContacts->toArray();

              if(count($beforeContacts) > 0){
                // Extrae los IDs de los arrays en $identificaciones
                $beforeContactIds = array_column($beforeContacts, 'id');
                $contactIds = array_column($contactos, 'id');
                $itemsContactToDelete = array_diff($beforeContactIds, $contactIds);
                          
                // ELIMINAR DOCUMENTOS
                Contact::whereIn('id', $itemsContactToDelete)->delete();
              }

           
              // REGISTRAR IDENTIFICACIONES DE LA EMPRESA
              foreach($identificaciones as $identity){
                $identity['empresas_id'] = $resCompany->id;
                $identity['user_create_id'] = $this->request->input('user_auth_id');

                $this->identificationValidator->setRequest($identity, $identity['id']);
                $validatorIdentity = $this->identificationValidator->validate();
    
                if ($validatorIdentity->fails()) {
                  $response = $this->responseError($validatorIdentity->errors(), 422);
                  DB::rollBack();
                  return $response;
                  break; // Detener el ciclo en caso de error
                } else {
                  // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
                  if(empty($identity['id'])){
                    $resultIdentificaciones[] = $this->identificationService->create($identity);
                  } else {
                    $resultIdentificaciones[] = $this->identificationService->update($identity, $identity['id']);
                  }
                }
              }

              // REGISTRAR CONTACTOS
              foreach($contactos as $contact){
                $contact['empresas_id'] = $resCompany->id;
                $contact['user_create_id'] = $this->request->input('user_auth_id');

                $this->contactValidator->setRequest($contact, $contact['id']);
                $validatorContact = $this->contactValidator->validate();
    
                if ($validatorContact->fails()) {
                  $response = $this->responseError($validatorContact->errors(), 422);
                  DB::rollBack();
                  return $response;
                  break; // Detener el ciclo en caso de error
                } else {
                  // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
                  if(empty($contact['id'])){
                    $resultContactos[] = $this->contactService->create($contact);
                  } else {
                    $resultContactos[] = $this->contactService->update($contact, $contact['id']);
                  }
                }
              }

              $resCompany->load('identifications', 'contacts', 'addresses');
              $resultFull['company'] = $resCompany;
            }
          }
        }

        $this->clientValidator->setRequest($dataClientWithoutPerson);
      } else {
        $dataClientWithoutCompany = $this->request->except(['empresas_id']);

        if(empty($dataPerson['codigo_ubigeo'])){
          unset($dataPerson['codigo_ubigeo']);
        }
   
        if(!empty($dataPerson)){
          $this->personValidator->setRequest($dataPerson, $dataPerson['id']);
          $validatorPerson = $this->personValidator->validate();
    
          if($validatorPerson->fails()){
            $response = $this->responseError($validatorPerson->errors(), 422);
            DB::rollBack();
            return $response;
          } else {
            // registrar persona
            $resPerson = $this->personService->update($dataPerson, $dataPerson['id']);
            if($resPerson){

              $identificaciones = $dataPerson['identifications'];
              $contactos = $dataPerson['contacts'];
  
              // DOCUMENTOS
              $beforeIdentifications = $resPerson->identifications;
              $beforeIdentifications = $beforeIdentifications->toArray();

              // Extrae los IDs de los arrays en $identificaciones
              if(count($beforeIdentifications) > 0){
                $beforeIds = array_column($beforeIdentifications, 'id');
                $identIds = array_column($identificaciones, 'id');
                $itemsToDelete = array_diff($beforeIds, $identIds);
                          
                // ELIMINAR DOCUMENTOS
                IdentificationDocument::whereIn('id', $itemsToDelete)->delete();
              }

              // CONTACTOS
              $beforeContacts = $resPerson->contacts;
              $beforeContacts = $beforeContacts->toArray();

              // Extrae los IDs de los arrays en $identificaciones
              if(count($beforeContacts) > 0){
                $beforeContactIds = array_column($beforeContacts, 'id');
                $contactIds = array_column($contactos, 'id');
                $itemsContactToDelete = array_diff($beforeContactIds, $contactIds);
                          
                // ELIMINAR DOCUMENTOS
                Contact::whereIn('id', $itemsContactToDelete)->delete();
              }
              
              // REGISTRAR IDENTIFICACIONES DE LA PERSONA
              foreach($identificaciones as $identity){
                $identity['personas_id'] = $resPerson->id;
                $identity['user_create_id'] = $this->request->input('user_auth_id');

                $this->identificationValidator->setRequest($identity, $identity['id']);
                $validatorIdentity = $this->identificationValidator->validate();
    
                if ($validatorIdentity->fails()) {
                  $response = $this->responseError($validatorIdentity->errors(), 422);
                  DB::rollBack();
                  return $response;
                  break; // Detener el ciclo en caso de error
                } else {
                  // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
                  if(empty($identity['id'])){
                    $resultIdentificaciones[] = $this->identificationService->create($identity);
                  } else {
                    $resultIdentificaciones[] = $this->identificationService->update($identity, $identity['id']);
                  }
                }
              }

              // REGISTRAR CONTACTOS
              foreach($contactos as $contact){
                $contact['personas_id'] = $resPerson->id;
                $contact['user_create_id'] = $this->request->input('user_auth_id');

                $this->contactValidator->setRequest($contact, $contact['id']);
                $validatorContact = $this->contactValidator->validate();
    
                if ($validatorContact->fails()) {
                  $response = $this->responseError($validatorContact->errors(), 422);
                  DB::rollBack();
                  return $response;
                  break; // Detener el ciclo en caso de error
                } else {
                  // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
                  if(empty($contact['id'])){
                    $resultContactos[] = $this->contactService->create($contact);
                  } else {
                    $resultContactos[] = $this->contactService->update($contact, $contact['id']);
                  }
                }
              }
    
              $resPerson->load('identifications', 'contacts', 'addresses');
              $resultFull['person'] = $resPerson;
            }
          }
        }

        $this->clientValidator->setRequest($dataClientWithoutCompany);
      }

      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
        DB::rollBack();
        return $response;
      } else {
        $resClient = $this->clientService->update($this->request->all(), $id);

          
        // DOCUMENTOS
        $beforeBankAccounts = $resClient->bankAccounts;
        $beforeBankAccounts = $beforeBankAccounts->toArray();

        if(count($beforeBankAccounts)){
          $beforeIds = array_column($beforeBankAccounts, 'id');
          // Extrae los IDs de los arrays en $identificaciones
          $bankAccountIds = array_column($cuentasBancarias, 'id');
          $itemsToDelete = array_diff($beforeIds, $bankAccountIds);

          // ELIMINAR DOCUMENTOS
          BankAccount::whereIn('id', $itemsToDelete)->delete();
        }           
        
        $resultCuentaBancarias = [];

        // REGISTRAR CONTACTOS
        foreach($cuentasBancarias as $bankAccount){
          $bankAccount['clientes_id'] = $resClient->id;
          
          $this->bankAccountValidator->setRequest($bankAccount, $bankAccount['id']);
          $validatorBankAccount = $this->bankAccountValidator->validate();
          
          if ($validatorBankAccount->fails()) {
            $response = $this->responseError($validatorBankAccount->errors(), 422);
            DB::rollBack();
            return $response;
            break; // Detener el ciclo en caso de error
          } else {
            // REGISTRAR O ACTUALIZAR LOS DATOS DE IDENTIFICACIONES
            if(empty($bankAccount['id'])){
              $bankAccount['user_create_id'] = $this->request->input('user_auth_id');
              $resultCuentaBancarias[] = $this->bankAccountService->create($bankAccount);
            } else {
              $bankAccount['user_update_id'] = $this->request->input('user_auth_id');
              $resultCuentaBancarias[] = $this->bankAccountService->update($bankAccount, $bankAccount['id']);
            }
          }
        }

        $resClient->load('bankAccounts');
        
        $resultFull['client'] = $resClient;
        $response = $this->responseUpdate($resultFull);
      }
  
      // Si todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      // Maneja la excepción, por ejemplo, muestra un mensaje de error
      DB::rollBack();
      return $this->responseError(['message' => 'Error al actualizar la venta', 'error' => $e->getMessage()], 422);
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch(\Exception $e){
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al modificar los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try {
      $result = $this->clientService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
      
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try {
      $result = $this->clientService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }

      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el cliente', 'error' => $e->getMessage()], 500);
    }
  }
}