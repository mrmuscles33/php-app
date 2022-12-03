<?php

class Column implements JsonSerializable
{

	private string $table;
	private string $name;
	private string $type;
	private int $size;
	private ?int $precision;
	private mixed $default;
	private bool $nullable = true;
	private bool $key = false;

	public function __construct(string $table, string $name)
	{
		$this->table = $table;
		$this->name = $name;
	}

	/**
	 * Return the column name
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Set the column name
	 * @param string $name 
	 * @return void
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * Return the column type
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * Set  the column type
	 * @param string $type 
	 * @return void
	 */
	public function setType(string $type): void
	{
		$this->type = $type;
	}

	/**
	 * Return the column size
	 * @return int
	 */
	public function getSize(): int
	{
		return $this->size;
	}

	/**
	 * Set the column size
	 * @param int $size 
	 * @return void
	 */
	public function setSize(int $size): void
	{
		$this->size = $size;
	}

	/**
	 * Return the column precision (for integer column only)
	 * @return int
	 */
	public function getPrecision(): int
	{
		return $this->precision;
	}

	/**
	 * Set the column precision (for integer column only)
	 * @param int $precision 
	 * @return void
	 */
	public function setPrecision(?int $precision): void
	{
		$this->precision = $precision;
	}

	/**
	 * Return the default column value
	 * @return mixed
	 */
	public function getDefault(): mixed
	{
		return $this->default;
	}

	/**
	 * Set the default column value
	 * @param mixed $default 
	 * @return void
	 */
	public function setDefault(mixed $default): void
	{
		$this->default = $default;
	}

	/**
	 * Return if column is nullable
	 * @return bool
	 */
	public function isNullable(): bool
	{
		return $this->nullable;
	}

	/**
	 * Set if column is nullable
	 * @param bool $nullable 
	 * @return void
	 */
	public function setNullable(bool $nullable): void
	{
		$this->nullable = $nullable;
	}

	/**
	 * Return if column is key
	 * @return bool
	 */
	public function isKey(): bool
	{
		return $this->key;
	}

	/**
	 * Set if column is key
	 * @param bool $key 
	 * @return void
	 */
	public function setKey(bool $key): void
	{
		$this->key = $key;
	}
	/**
	 * @return string
	 */
	public function getTable(): string
	{
		return $this->table;
	}

	/**
	 * @param string $table 
	 * @return void
	 */
	public function setTable(string $table): void
	{
		$this->table = $table;
	}

	/**
	 * Return string for echo or printf method
	 * @return string
	 */
	public function __toString()
	{
		return json_encode($this);
	}

	/**
	 * Convert to JSON
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return (array)get_object_vars($this);
	}
}

?>