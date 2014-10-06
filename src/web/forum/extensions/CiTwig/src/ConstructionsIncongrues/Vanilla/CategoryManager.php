<?php
namespace ConstructionsIncongrues\Vanilla;

class CategoryManager extends \CategoryManager
{
	public function GetCategories($IncludeCount = '0', $OrderByPreference = '0', $ForceRoleBlock = '1')
	{
		$OrderByPreference = ForceBool($OrderByPreference, 0);
		$s = $this->GetCategoryBuilder($IncludeCount, $ForceRoleBlock);
		if ($OrderByPreference && $this->Context->Session->UserID > 0) {
			// Order by the user's preference (unblocked categories first)
			$s->AddOrderBy('Blocked', 'b', 'asc');
		}
		$s->AddOrderBy('Priority', 'c', 'asc');

		// Create categories array from database results
		$dbResults = $this->Context->Database->Select($s, $this->Name, 'GetCategories', 'An error occurred while retrieving categories.');
		$categories = array();
		while ($Row = $this->Context->Database->GetRow($dbResults)) {
			$Category = $this->Context->ObjectFactory->NewObject($this->Context, 'Category');
			$Category->Clear();
			$Category->GetPropertiesFromDataSet($Row);
			$Category->FormatPropertiesForDisplay();
			$categories[] = $Category;
		}

		return $categories;
	}
}