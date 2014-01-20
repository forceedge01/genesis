<?php

namespace Bundles\Clients\Interfaces;


/**
 *
 * @group groupName
 * @author John Doe <john.doe@example.com>
 *
 */
interface ClientsControllerInterface {

    /**
     *
     * @author <Above>
     *
     * @return type Description
     *
     * @example path description
     *
     */
    public function indexAction();

    /**
     *
     * @author <Above>
     *
     * @return type Description
     *
     * @example path description
     *
     */
    public function listAction();

    /**
     *
     * @author <Above>
     *
     * @param type $id Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function viewAction($id);

    /**
     *
     * @author <Above>
     *
     * @param type $id Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function editAction($id);

    /**
     *
     * @author <Above>
     *
     * @return type Description
     *
     * @example path description
     *
     */
    public function createAction();

    /**
     *
     * @author <Above>
     *
     * @param type $id Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function deleteAction($id);
}
