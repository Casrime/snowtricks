<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Service\FileHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/image')]
class ImageController extends AbstractController
{
    #[Route('/new', name: 'app_image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileHandler $fileHandler, EntityManagerInterface $entityManager): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ?UploadedFile $imageFile */
            $imageFile = $form->get('name')->getData();
            if ($imageFile) {
                $imageFileName = $fileHandler->upload($imageFile);
                $image->setName($imageFileName);
            }
            $entityManager->persist($image);
            $entityManager->flush();

            $this->addFlash('success', 'Image created successfully');

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('common/new.html.twig', [
            'image' => $image,
            'form' => $form,
            'name' => 'image',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Image $image, FileHandler $fileHandler, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ?UploadedFile $imageFile */
            $imageFile = $form->get('name')->getData();
            if ($imageFile) {
                $imageFileName = $fileHandler->upload($imageFile);
                $image->setName($imageFileName);
            }
            $entityManager->flush();

            $this->addFlash('warning', 'Image updated successfully');

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('common/edit.html.twig', [
            'image' => $image,
            'form' => $form,
            'name' => 'image',
        ]);
    }

    #[Route('/{id}', name: 'app_image_delete', methods: ['POST'])]
    public function delete(Request $request, Image $image, EntityManagerInterface $entityManager, TrickRepository $trickRepository): Response
    {
        $token = $request->request->getString('_token');
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $token)) {
            $tricks = $trickRepository->findAll();
            foreach ($tricks as $trick) {
                if ($trick->getMainImage() === $image) {
                    $trick->setMainImage(null);
                }
                $trick->removeImage($image);
            }
            $entityManager->remove($image);
            $entityManager->flush();

            $this->addFlash('success', 'Image deleted successfully');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
    }
}
