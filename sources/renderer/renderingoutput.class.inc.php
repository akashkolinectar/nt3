<?php

namespace Combodo\nt3\Renderer;

/**
 * Description of RenderingOutput
 */
class RenderingOutput
{
	protected $sHtml;
	protected $sJsInline;
	protected $aJsFiles;
	protected $sCssInline;
	protected $aCssFiles;
	protected $aCssClasses;

	public function __construct()
	{
		$this->sHtml = '';
		$this->sJsInline = '';
		$this->aJsFiles = array();
		$this->sCssInline = '';
		$this->aCssFiles = array();
		$this->aCssClasses = array();
	}

	/**
	 *
	 * @return string
	 */
	public function GetHtml()
	{
		return $this->sHtml;
	}

	/**
	 *
	 * @return string
	 */
	public function GetJs()
	{
		return $this->sJsInline;
	}

	/**
	 *
	 * @return array
	 */
	public function GetJsFiles()
	{
		return $this->aJsFiles;
	}

	/**
	 *
	 * @return string
	 */
	public function GetCss()
	{
		return $this->sCssInline;
	}

	/**
	 *
	 * @return array
	 */
	public function GetCssFiles()
	{
		return $this->aCssFiles;
	}

    /**
     *
     * @return array
     */
	public function GetCssClasses()
    {
        return $this->aCssClasses;
    }

	/**
	 *
	 * @param string $sHtml
	 * @return \Combodo\nt3\Renderer\RenderingOutput
	 */
	public function AddHtml($sHtml, $bEncodeHtmlEntities = false)
	{
		$this->sHtml .= ($bEncodeHtmlEntities) ? htmlentities($sHtml, ENT_QUOTES, 'UTF-8') : $sHtml;
		return $this;
	}

	/**
	 *
	 * @param string $sJs
	 * @return \Combodo\nt3\Renderer\RenderingOutput
	 */
	public function AddJs($sJs)
	{
		$this->sJsInline .= $sJs . "\n";
		return $this;
	}

	/**
	 *
	 * @param string $sFile
	 * @return \Combodo\nt3\Renderer\RenderingOutput
	 */
	public function AddJsFile($sFile)
	{
		if (!in_array($sFile, $this->aJsFiles))
		{
			$this->aJsFiles[] = $sFile;
		}
		return $this;
	}

	/**
	 *
	 * @param string $sFile
	 * @return \Combodo\nt3\Renderer\RenderingOutput
	 */
	public function RemoveJsFile($sFile)
	{
		if (in_array($sFile, $this->aJsFiles))
		{
			unset($this->aJsFiles[$sFile]);
		}
		return $this;
	}

	/**
	 *
	 * @param string $sCss
	 * @return \Combodo\nt3\Renderer\RenderingOutput
	 */
	public function AddCss($sCss)
	{
		$this->sCssInline .= $sCss . "\n";
		return $this;
	}

    /**
     *
     * @param string $sFile
     * @return \Combodo\nt3\Renderer\RenderingOutput
     */
    public function AddCssFile($sFile)
    {
        if (!in_array($sFile, $this->aCssFiles))
        {
            $this->aCssFiles[] = $sFile;
        }
        return $this;
    }

    /**
     *
     * @param string $sFile
     * @return \Combodo\nt3\Renderer\RenderingOutput
     */
    public function RemoveCssFile($sFile)
    {
        if (in_array($sFile, $this->aCssFiles))
        {
            unset($this->aCssFiles[$sFile]);
        }
        return $this;
    }

    /**
     *
     * @param string $sClass
     * @return \Combodo\nt3\Renderer\RenderingOutput
     */
    public function AddCssClass($sClass)
    {
        if (!in_array($sClass, $this->aCssClasses))
        {
            $this->aCssClasses[] = $sClass;
        }
        return $this;
    }

    /**
     *
     * @param string $sClass
     * @return \Combodo\nt3\Renderer\RenderingOutput
     */
    public function RemoveCssClass($sClass)
    {
        if (in_array($sClass, $this->aCssClasses))
        {
            unset($this->aCssClasses[$sClass]);
        }
        return $this;
    }

}
