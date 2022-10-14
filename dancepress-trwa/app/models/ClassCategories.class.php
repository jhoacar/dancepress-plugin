<?php
namespace DancePressTRWA\Models;

class ClassCategories extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function getCategories()
    {
        return $this->db->get_results("SELECT id,category_name, REPLACE(LOWER(category_name),' ','') as slug_name FROM {$this->p}ds_class_categories WHERE " . $this->sessionCondition);
    }

    public function getClassCategories()
    {
        return $this->getCategories();
    }

    public function getAllClassCategories()
    {
        return $this->getCategories();
    }

    public function getClassCategoryById($id)
    {
        $id = (int)$id;

        $sql = $this->db->prepare(
            "
				select
				*
				FROM
					{$this->p}ds_class_categories
					where
				id=%s
				AND " .
                    $this->sessionCondition,
            $id
        );


        $this->db->query($sql);
        $res = $this->db->last_result;

        return $res;
    }

    public function addNewClassCategory($input)
    {
        $sql = $this->db->prepare(
            "
			INSERT INTO
				{$this->p}ds_class_categories
			SET
				category_name = %s, " .
            $this->sessionCondition,
            $input['category_name']
        );

        $this->db->query($sql);
        return $this->db->last_result;
    }

    public function adminUpdateClassCategory($input)
    {
        $sql = $this->db->prepare(
            "
			UPDATE
				{$this->p}ds_class_categories
			SET
				category_name = %s
				where
					id=%s
				AND " .
            $this->sessionCondition,
            $input['category_name'],
            $input['id']
        );

        $this->db->query($sql);

        return $this->db->last_result;
    }

    public function deleteClassCategory($ids)
    {
        if (empty($ids)) {
            return "Classes not deleted";
        }

        $ids = array_filter($ids, 'ctype_digit');

        $sql = $this->db->prepare(
            "
				DELETE FROM
					{$this->p}ds_class_categories
				WHERE
					id in (%s)
				AND " .
                    $this->sessionCondition,
            implode(',', $ids)
        );

        $this->db->query($sql);

        return "Class deleted.";
    }
}
