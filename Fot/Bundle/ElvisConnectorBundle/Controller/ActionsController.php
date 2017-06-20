<?php
/**
 * Created by PhpStorm.
 * User: PerigeeSoftouaire
 * Date: 22/05/2017
 * Time: 11:55
 */

namespace Fot\Bundle\ElvisConnectorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class ActionsController extends Controller
{
    public function indexAction()
    {

        return new JsonResponse("[{\"code\":\"images_bag_jpg\",\"localized\":false,\"description\":null,\"end_of_use\":null,\"tags\":[\"front\",\"hd\",\"picture\"],\"categories\":[\"images\",\"print\"],\"references\":[{\"locale\":null,\"file\":\"a/4/8/1/a4813dc91ff573a094283f80f4cb8805300826e6_bag.jpg\"}]},{\"code\":\"images_belt_jpg\",\"localized\":false,\"description\":null,\"end_of_use\":null,\"tags\":[\"hd\",\"image\",\"important\",\"large\",\"todo\"],\"categories\":[\"images\",\"situ\",\"prioritized_images\"],\"references\":[{\"locale\":null,\"file\":\"0/1/7/d/017de1464091afb55123d765f07ee92d0ab0e209_belt.jpg\"}]},{\"code\":\"images_hera_jpg\",\"localized\":false,\"description\":null,\"end_of_use\":null,\"tags\":[\"front\",\"hd\",\"picture\"],\"categories\":[\"images\",\"print\"],\"references\":[{\"locale\":null,\"file\":\"c/f/5/b/cf5b6e787d668153dd28a3a068f09cb4cb6ed7c9_hera.jpg\"}]}]");
    }

    public function getCategorieRootsAction(Request $request)
    {
        $code = "/";
        $curlRequest = $this->container->get('ElvisConnector.Curl');
        $result = $curlRequest->listCategories($code);
        $formatted_result = array();
        foreach ($result as $dir)
            array_push($formatted_result, array(
                "id" => $dir->name,
                "label" => $dir->name,
                "code" => $dir->assetPath,
                "selected" => "false"
            ));
        return new JsonResponse($formatted_result);
    }

    public function getCategorieAction(Request $request)
    {
        //$code = $request->request->get('code');
        $code = "";
        $getCode = $request->query->get('id');
        if ($getCode) $code = substr($getCode, 0, 1) == '/' ? $getCode : '/' . $getCode;
        $curlRequest = $this->container->get('ElvisConnector.Curl');
        $result = $curlRequest->listCategories($code);
        $formatted_result = array();
        foreach ($result as $dir)
            array_push($formatted_result, array(
                "attr" => array(
                    "id" => "node_" . $code . "/" . $dir->name,
                    "data-code" => $dir->name),
                "data" => $dir->name,

                "state" => "closed"));
        return new JsonResponse($formatted_result);
    }

    public function getCategorieContentAction(Request $request)
    {
        $page = "";
        $perPage = "25";
        $pager = $request->query->get("asset-picker-grid");
        $currentPage = 1;
        if ($pager) {
            $perPage = (int)$pager["_pager"]["_per_page"];
            $currentPage = (int)$pager["_pager"]["_page"];
            $page = ($currentPage - 1) * $perPage;
        }
        //filter root
        $idParent = "/" . $pager["_filter"]["category"]["value"]["treeId"];
        //filtre nom
        $assetName = isset($pager["_filter"]["name"]) ? $pager["_filter"]["name"]["value"] : false;
        $assetNameFilterType = isset($pager["_filter"]["name"]) ? $pager["_filter"]["name"]["type"] : false;
        //filter tree
        $idChildren = $pager["_filter"]["category"]["value"]["categoryId"];
        //filter fileType
        $filterType = isset($pager["_filter"]["type"]) ? $pager["_filter"]["type"]["value"] : false;;
        $code = $idChildren ? $idChildren : $idParent;
        //filtre date
        $filterDate = null ;
        if(isset($pager["_filter"]["modifdate"]))

            
        $curlRequest = $this->container->get('ElvisConnector.Curl');

        $result = $curlRequest->listCategorieContent($code, $page, $perPage, $assetName, $filterType, $assetNameFilterType);
        //return New Response($result);
        $totalHits = $result->totalHits;
        $cred = $result->cred;
        $hits = $result->hits;
        $filterStringChoices = array(
            array("attr" => array(), "label" => "contient", "value" => "1", "data" => 1),
            array("attr" => array(), "label" => "commence par", "value" => "4", "data" => 4),
        );

        $filterTypeAsset = array(
            array("label" => "Image", "value" => "1"),
            array("label" => "Container", "value" => "2"),
        );

        $fitlerDateChoices = array(
            array("attr" => array(), "label" => "compris entre", "value" => "1", "data" => 1),
            array("attr" => array(), "label" => "non compris entre", "value" => "2", "data" => 2),
            array("attr" => array(), "label" => "supérieur à", "value" => "3", "data" => 3),
            array("attr" => array(), "label" => "inférieur à", "value" => "4", "data" => 4),
        );

        $result = array(
            "metadata" => array(
                "requireJSModules" => array("pim/datagrid/column-form-listener", "oro/datafilter-builder"),
                "options" => array(
                    "gridName" => "asset-picker-grid",
                    "url" => "/elvis-categorie-content?id=$code",
                    "totalRecords" => $totalHits,
                    "entityHint" => "elvisAsset",
                    "columnListener" => array(
                        "dataField" => "name",
                        "columnName" => "is_checked"
                    ),
                    "routerEnabled" => false,
                    "toolbarOptions" => array(
                        "hide" => false,
                        "pageSize" => array(
                            "hide" => false,
                            "default_per_page" => $perPage,
                            "items" => array(10, 25, 50, 100)
                        ),
                        "pagination" => array("hide" => false),
                        "multipleSorting" => false,
                        "url" => "/elvis-categorie-content?id=$code"

                    )
                ),
                "columns" => array(
                    array("label" => "Is_checked", "width" => 10, "type" => "boolean", "editable" => true, "renderable" => true, "name" => "is_checked"),
                    array("label" => "Miniature", "template" => "PimEnterpriseProductAssetBundle:Property:thumbnail.html.twig", "type" => "html", "selector" => "asset_thumbnail", "editable" => false, "renderable" => true, "name" => "references"),
                    array("label" => "Name", "type" => "string", "editable" => false, "renderable" => true, "name" => "name", "sortable" => true),
                    array("label" => "Type", "type" => "string", "editable" => false, "renderable" => true, "name" => "type", "sortable" => true),
                    array("label" => "Créé par", "type" => "string", "editable" => false, "renderable" => true, "name" => "createdby", "sortable" => true),
                    array("label" => "Date de modification", "type" => "date", "editable" => false, "renderable" => true, "name" => "modifdate", "sortable" => true),
                    array("label" => "Code", "type" => "string", "editable" => false, "renderable" => false, "name" => "code", "sortable" => false)
                ),
                "state" => array("filters" => array(), "sorters" => array(), "currentPage" => $currentPage, "pageSize" => $perPage),
                "filters" => array(
                    array("name" => "name", "label" => "Name", "choices" => $filterStringChoices, "enabled" => true, "type" => "string"),
                    array("name" => "type", "label" => "Type", "choices" => $filterTypeAsset, "enabled" => true, "type" => "choice"),
                    array("name" => "modifdate", "label" => "Date de modification", "choices" => $fitlerDateChoices, "enabled" => true, "type" => "date"),
                )
            ),
            "data" => array("data" => array(), "options" => array("totalRecords" => $totalHits)),
            "options" => array(
                "gridName" => "asset-picker-grid",
                "url" => "/elvis-categorie-content?id=$code",
                "totalRecords" => $totalHits,
                "entityHint" => "elvisAsset",
                "columnListener" => array(
                    "dataField" => "name",
                    "columnName" => "is_checked"
                ),
                "parse" => "false")
        );

        foreach ($hits as $hit) {
            array_push($result["data"]["data"], array(
                "is_checked" => null,
                "references" => isset($hit->thumbnailUrl) ? "<img src=\"" . $hit->thumbnailUrl . "&authcred=$cred\"  width =\"80px;\">" : '',
                "name" => isset($hit->metadata->filename) ? $hit->metadata->filename : '',
                "type" => isset($hit->metadata->assetDomain) ? $hit->metadata->assetDomain : '',
                "id" => $hit->id,
                "createdby" => $hit->metadata->assetCreator,
                "modifdate" => date("d-m-Y h:i", strtotime($hit->metadata->assetFileModified->formatted)),
                "code" => $hit->id

            ));

        }
if ($pager) return new JsonResponse(["data" => $result["data"]["data"], "options" => $result["options"]]);
        return new JsonResponse($result);
    }


    public function getProductAssetsAction(Request $request)
    {

        $assets = str_replace(',', " || ", $request->query->get('assets'));
        if ($assets) {
            $curlRequest = $this->container->get('ElvisConnector.Curl');
            $result = $curlRequest->getAssets($assets);
            $cred = $result->cred;
            $hits = $result->hits;
            $result = array();
            foreach ($hits as $hit) {
                array_push($result,
                    array(
                        "code" => $hit->id,
                        "localized" => "false",
                        "description" => isset($hit->metadata->filename) ? $hit->metadata->filename : '',
                        "end_of_use" => "2017-03-28T00:00:00+02:00",
                        "file" => isset($hit->thumbnailUrl) ? $hit->thumbnailUrl . "&authcred=$cred" : "",
                        "tags" => array(),
                        "categories" => array(),
                        "reference" => array(
                            "locale" => "",
                            "file" => isset($hit->thumbnailUrl) ? $hit->thumbnailUrl . "&authcred=$cred" : ""
                        )
                    )
                );
            }
            return new JsonResponse($result);
        }
        return new JsonResponse();

    }


    public function testAction(Request $request)
    {
        $code = "/akeneo";
        $getCode = $request->query->get('id');
        if ($getCode) $code = substr($getCode, 0, 1) == '/' ? $getCode : '/' . $getCode;
        $curlRequest = $this->container->get('ElvisConnector.Curl');
        $result = $curlRequest->listCategorieContent($code);
        return new JsonResponse($result);
    }

}