<?php

namespace App\Customs;

use App\Helpers\ExcelHelper;

class ExcelCellTracker
{
    public int $currentRowTrackerStartCell;
    public int $currentRowTrackerEndCell;
    public int $currentColumnTrackerStartCell;
    public int $currentColumnTrackerEndCell;

    public function __construct(int $startColumn = 1, int $startRow = 1)
    {
        $this->currentColumnTrackerStartCell = $startColumn;
        $this->currentColumnTrackerEndCell = $startColumn;
        $this->currentRowTrackerStartCell = $startRow;
        $this->currentRowTrackerEndCell = $startRow;
    }
    public function addColumn(int $addValue = 1): void
    {
        $this->currentColumnTrackerEndCell += $addValue;
    }
    public function addRow(int $addValue = 1): void
    {
        $this->currentRowTrackerEndCell += $addValue;
    }
    public function setEndColumn(int $value): void
    {
        $this->currentColumnTrackerEndCell = $value;
    }
    public function setEndRow(int $value): void
    {
        $this->currentRowTrackerEndCell = $value;
    }

    public function getStartCellInExcelNotation(): string
    {
        return ExcelHelper::numberToAlphabet($this->currentColumnTrackerStartCell) . $this->currentRowTrackerStartCell;
    }
    public function getEndCellInExcelNotation(): string
    {
        return ExcelHelper::numberToAlphabet($this->currentColumnTrackerEndCell) . $this->currentRowTrackerEndCell;
    }
}