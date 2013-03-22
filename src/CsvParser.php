<?php
namespace CsvParser;

class CsvParser
{
    /**
     * @var array
     */
    protected $defaults = [
        'delimiter' => ';',
        'line_delimiter' => "\n",
        'source_encoding' => 'utf-8',
        'output_encoding' => 'utf-8',
        'replace_linebreaks' => true,
        'header_as_keys' => true,
    ];

    /**
     * @param string $src
     * @param array $options
     * @return array
     */
    public function parse($src, $options = [])
    {
        $options = array_merge($this->defaults, $options);

        if (!$src = file_get_contents($src)) {
            throw new \Exception('file not found');
        }

        $output = [];
        $keys = [];

        foreach(explode("\n", $src) as $lineIndex => $line) {
            if ($options['source_encoding'] != $options['output_encoding']) {
                iconv($options['source_encoding'], $options['output_encoding'], $line);
            }

            $line = str_getcsv($line, $options['delimiter']);

            foreach($line as &$linePart) {
                $linePart = trim($options['replace_linebreaks'] ? str_replace(array("\n", "\r"), '', $linePart) : $linePart);
            }

            if ($options['header_as_keys']) {
                if (!$lineIndex) {
                    $keys = array_filter($line);

                    continue;
                }
            } else {
                $keys = array_keys($line);
            }

            $output[] = array_combine($keys, $line);
        }

        return $output;
    }
}