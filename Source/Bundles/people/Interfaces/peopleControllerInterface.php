<?php

namespace Application\Bundles\people\Interfaces;


/**
 *
 * @group groupName
 * @author John Doe <john.doe@example.com>
 *
 */
interface peopleControllerInterface {

    /**
     *
     * @author <Above>
     *
     * @param type $name Description
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
     * @param type $name Description
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
     * @param type $name Description
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
     * @param type $name Description
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
     * @param type $name Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function deleteAction($id);
}

