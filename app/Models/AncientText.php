<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AncientText extends Model
{
    protected $fillable = ['team_id', 'title', 'content', 'volume', 'page'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
    
    public function getPaginatedContent($linesPerPage = 12)
    {
        $lines = $this->splitContentIntoLines($this->content);
        return array_chunk($lines, $linesPerPage);
    }

    private function splitContentIntoLines($content)
    {
        // 确保内容是 UTF-8 编码
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        
        // 按换行符分割段落
        $paragraphs = explode("\n", $content);
        $finalLines = [];
        
        // 处理每个段落
        foreach ($paragraphs as $paragraph) {
            if (trim($paragraph) === '') continue;
            // 仅移除首尾空白字符，不移除句子间的空格
            $paragraph = trim($paragraph);
            
            $maxCharsPerLine = 27; // 非首行最大字符数
            $currentLine = '';
            $paragraphLines = [];
            $isFirstLine = true;

            // 按字符逐个处理
            $chars = preg_split('//u', $paragraph, -1, PREG_SPLIT_NO_EMPTY);
            $i = 0;

            while ($i < count($chars)) {
                $remainingSpace = $isFirstLine ? $maxCharsPerLine - 2 : $maxCharsPerLine;

                // 收集字符直到达到每行最大字符数
                while ($i < count($chars) && mb_strlen($currentLine) < $remainingSpace) {
                    $currentLine .= $chars[$i];
                    $i++;
                }

                // 移除首尾空白字符（仅针对当前行）
                $currentLine = trim($currentLine);
                if ($currentLine === '') {
                    $currentLine = '';
                    continue;
                }

                $paragraphLines[] = $currentLine;
                $currentLine = '';
                $isFirstLine = false;
            }

            // 为每行添加首行标记
            foreach ($paragraphLines as $lineIndex => $line) {
                $finalLines[] = [
                    'content' => $line,
                    'isFirstLineOfParagraph' => $lineIndex === 0
                ];
            }
        }
        
        return $finalLines;
    }
}