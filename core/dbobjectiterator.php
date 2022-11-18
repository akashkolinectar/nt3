<?php

/**
 * A set of persistent objects, could be heterogeneous as long as the objects in the set have a common ancestor class 
 *
 * @package     nt3ORM
 */
interface iDBObjectSetIterator extends Countable
{
	/**
	 * The class of the objects of the collection (at least a common ancestor)
	 *
	 * @return string
	 */
	public function GetClass();

	/**
	 * The total number of objects in the collection
	 *
	 * @return int
	 */
	public function Count();

	/**
	 * Reset the cursor to the first item in the collection. Equivalent to Seek(0)
	 *
	 * @return DBObject The fetched object or null when at the end
	 */
	public function Rewind();

	/**
	 * Position the cursor to the given 0-based position
	 *
	 * @param int $iRow
	 */
	public function Seek($iPosition);

	/**
	 * Fetch the object at the current position in the collection and move the cursor to the next position.
	 *
	 * @return DBObject The fetched object or null when at the end
	 */
	public function Fetch();
}
