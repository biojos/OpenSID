<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * File ini:
 *
 * Controller untuk modul Buku Pembangunan Desa > Buku Rencana Pembangunan
 *
 * donjo-app/controllers/Bumindes_rencana_pembangunan.php,
 *
 */

/**
 *
 * File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:
 *
 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.
 *
 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package	OpenSID
 * @author	Tim Pengembang OpenDesa
 * @copyright	Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright	Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license	http://www.gnu.org/licenses/gpl.html	GPL V3
 * @link 	https://github.com/OpenSID/OpenSID
 */

class Bumindes_rencana_pembangunan extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pembangunan_model', 'model');
		$this->load->model('pamong_model');
		$this->modul_ini = 301;
		$this->sub_modul_ini = 305;
		$this->set_minsidebar(1);
	}

	public function index()
	{
		if ($this->input->is_ajax_request())
		{
			$start = $this->input->post('start');
			$length = $this->input->post('length');
			$search = $this->input->post('search[value]');
			$order = $this->model::ORDER_ABLE[$this->input->post('order[0][column]')];
			$dir = $this->input->post('order[0][dir]');
			$tahun = $this->input->post('tahun');
			
			return $this->json_output([
				'draw' => $this->input->post('draw'),
				'recordsTotal' => $this->model->get_data()->count_all_results(),
				'recordsFiltered' => $this->model->get_data($search, $tahun)->count_all_results(),
				'data' => $this->model->get_data($search, $tahun)->order_by($order, $dir)->limit($length, $start)->get()->result(),
			]);
		}

		$this->render('bumindes/pembangunan/main', [
			'list_tahun' => $this->model->list_filter_tahun(),
			'selected_nav' => 'rencana',
			'subtitle' => 'Buku Rencana Pembangunan',
			'main_content' => 'bumindes/pembangunan/rencana_kerja/index',
		]);
	}

	public function dialog($aksi = '')
	{
		$data = [
			'aksi' => $aksi,
			'form_action' => site_url("bumindes_rencana_pembangunan/cetak/$aksi"),
			'isi' => "bumindes/pembangunan/rencana_kerja/ajax_dialog",
			'list_tahun' => $this->model->list_filter_tahun(),
		];

		$this->load->view('global/dialog_cetak', $data);
	}

	public function cetak($aksi = '')
	{
		$tahun = $this->input->post('tahun');

		$data = [
			'aksi' => $aksi,
			'config' => $this->header['desa'],
			'pamong_ketahui' => $this->pamong_model->get_ttd(),
			'pamong_ttd' => $this->pamong_model->get_ub(),
			'main' => $this->model->get_data('', $tahun)->get()->result(),
			'tgl_cetak' => $this->input->post('tgl_cetak'),
			'file' => "Buku Rencana Kerja Pembangunan",
			'isi' => "bumindes/pembangunan/rencana_kerja/cetak",
			'letak_ttd' => ['2', '2', '5'],
		];

		$this->load->view('global/format_cetak', $data);
	}

	// Lainnya
	public function lainnya($submenu)
	{
		$this->render('bumindes/pembangunan/main', [
			'selected_nav' => $submenu,
			'main_content' => 'bumindes/pembangunan/content_rencana'
		]);
	}
}
