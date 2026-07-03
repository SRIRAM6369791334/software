<?php $__env->startSection('title', 'Assign Permissions'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="permissionManager()">
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Assign Permissions','subtitle' => 'Configure access rights for the '.e($role->name).' role','backRoute' => 'admin.roles.index']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Assign Permissions','subtitle' => 'Configure access rights for the '.e($role->name).' role','backRoute' => 'admin.roles.index']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <div class="flex gap-2">
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','@click' => 'selectAll()','variant' => 'secondary','icon' => 'check_box','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','@click' => 'selectAll()','variant' => 'secondary','icon' => 'check_box','size' => 'sm']); ?>
                    Select All
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','@click' => 'deselectAll()','variant' => 'ghost','icon' => 'check_box_outline_blank','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','@click' => 'deselectAll()','variant' => 'ghost','icon' => 'check_box_outline_blank','size' => 'sm']); ?>
                    Clear All
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            </div>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

    <form action="<?php echo e(route('admin.roles.assignPermission')); ?>" method="POST" id="permissionsForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="role_id" value="<?php echo e($role->id); ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
            <?php $__currentLoopData = $permissionGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white/60 dark:bg-zinc-900/60 backdrop-blur-xl border border-white/80 dark:border-zinc-800/80 rounded-[2rem] overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] transition-all duration-500 group flex flex-col h-full">
                
                
                <div class="px-6 py-5 border-b border-zinc-100/80 dark:border-zinc-800/80 bg-gradient-to-br from-white/80 to-white/40 dark:from-zinc-900/80 dark:to-zinc-900/40 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-500/20 dark:to-teal-500/20 flex items-center justify-center border border-emerald-100 dark:border-emerald-500/20 shadow-inner">
                            <span class="material-symbols-rounded text-emerald-600 dark:text-emerald-400 text-xl">
                                <?php echo e(match(strtolower($group->name)) {
                                    'administration' => 'admin_panel_settings',
                                    'finance & payments' => 'account_balance_wallet',
                                    'inventory & stock' => 'inventory_2',
                                    'master records' => 'folder_open',
                                    'operations' => 'storefront',
                                    'performance' => 'monitoring',
                                    'routes & delivery' => 'local_shipping',
                                    'sales reports' => 'bar_chart',
                                    default => 'shield_lock'
                                }); ?>

                            </span>
                        </div>
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-zinc-100 tracking-tight"><?php echo e($group->name); ?></h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium" x-text="`${selectedInGroup('group-<?php echo e($group->id); ?>')} of <?php echo e($group->permissions->count()); ?> selected`"></p>
                        </div>
                    </div>
                    
                    
                    <label class="relative inline-flex items-center cursor-pointer" title="Toggle all in <?php echo e($group->name); ?>">
                        <input type="checkbox" class="sr-only peer group-toggle" data-group="group-<?php echo e($group->id); ?>" @change="toggleGroup('group-<?php echo e($group->id); ?>', $event.target.checked)" :checked="isGroupFullySelected('group-<?php echo e($group->id); ?>')">
                        <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-zinc-600 peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                
                <div class="p-4 space-y-2 flex-grow bg-gradient-to-b from-transparent to-zinc-50/30 dark:to-zinc-900/30">
                    <?php $__currentLoopData = $group->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="relative flex items-center p-3 rounded-xl border border-transparent hover:border-emerald-100 hover:bg-emerald-50/50 dark:hover:border-emerald-500/20 dark:hover:bg-emerald-500/5 cursor-pointer transition-all duration-300 group/item overflow-hidden"
                           :class="{'bg-emerald-50/80 border-emerald-200/60 dark:bg-emerald-500/10 dark:border-emerald-500/30 shadow-sm': isChecked('perm-<?php echo e($permission->id); ?>')}">
                        
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="permissions[]" value="<?php echo e($permission->id); ?>" 
                                id="perm-<?php echo e($permission->id); ?>"
                                class="perm-checkbox group-<?php echo e($group->id); ?> w-5 h-5 rounded-lg border-zinc-300 text-emerald-600 focus:ring-emerald-500/30 focus:ring-offset-0 dark:border-zinc-700 dark:bg-zinc-800 dark:checked:bg-emerald-500 transition-all cursor-pointer"
                                <?php echo e($role->hasPermissionTo($permission->name) ? 'checked' : ''); ?>

                                @change="updateSelection('perm-<?php echo e($permission->id); ?>')">
                        </div>
                        <div class="ml-3 flex flex-col">
                            <span class="text-sm font-semibold transition-colors duration-300"
                                  :class="isChecked('perm-<?php echo e($permission->id); ?>') ? 'text-emerald-800 dark:text-emerald-300' : 'text-zinc-700 dark:text-zinc-300 group-hover/item:text-zinc-900 dark:group-hover/item:text-zinc-100'">
                                <?php echo e(str_replace(' ', ' ', ucwords($permission->name))); ?>

                            </span>
                            <span class="text-xs text-zinc-400 dark:text-zinc-500 font-jetbrains tracking-wide">
                                <?php echo e($permission->name); ?>

                            </span>
                        </div>

                        
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-r-full transform -translate-x-full transition-transform duration-300"
                             :class="{'translate-x-0': isChecked('perm-<?php echo e($permission->id); ?>')}"></div>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 transition-all duration-500"
             :class="hasChanges ? 'translate-y-0 opacity-100' : 'translate-y-20 opacity-0 pointer-events-none'">
            <div class="bg-zinc-900/90 dark:bg-zinc-100/90 backdrop-blur-xl px-6 py-4 rounded-full shadow-2xl border border-zinc-800 dark:border-zinc-200 flex items-center gap-6">
                <div class="text-sm font-medium text-white dark:text-zinc-900 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400 dark:bg-emerald-500/20 dark:text-emerald-600 text-xs font-bold" x-text="changedCount"></span>
                    unsaved changes
                </div>
                <div class="h-6 w-px bg-zinc-700 dark:bg-zinc-300"></div>
                <div class="flex gap-3">
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','@click' => 'reset()','variant' => 'ghost','class' => 'text-zinc-300 hover:text-white dark:text-zinc-600 dark:hover:text-zinc-900 border-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','@click' => 'reset()','variant' => 'ghost','class' => 'text-zinc-300 hover:text-white dark:text-zinc-600 dark:hover:text-zinc-900 border-none']); ?>
                        Discard
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage roles')): ?>
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'primary','icon' => 'save','class' => 'rounded-full shadow-[0_0_20px_rgba(16,185,129,0.4)]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','icon' => 'save','class' => 'rounded-full shadow-[0_0_20px_rgba(16,185,129,0.4)]']); ?>
                            Save Changes
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="mt-8 flex justify-end gap-3 pb-24">
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('admin.roles.index')).'','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.roles.index')).'','variant' => 'secondary']); ?>Cancel <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage roles')): ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'primary','icon' => 'save']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','icon' => 'save']); ?>Save Permissions <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('permissionManager', () => ({
            initialState: {},
            currentState: {},
            
            init() {
                document.querySelectorAll('.perm-checkbox').forEach(cb => {
                    this.initialState[cb.id] = cb.checked;
                    this.currentState[cb.id] = cb.checked;
                });
            },
            
            get hasChanges() {
                for (const id in this.initialState) {
                    if (this.initialState[id] !== this.currentState[id]) return true;
                }
                return false;
            },
            
            get changedCount() {
                let count = 0;
                for (const id in this.initialState) {
                    if (this.initialState[id] !== this.currentState[id]) count++;
                }
                return count;
            },
            
            isChecked(id) {
                return this.currentState[id] || false;
            },
            
            updateSelection(id) {
                const cb = document.getElementById(id);
                if (cb) {
                    this.currentState[id] = cb.checked;
                }
            },
            
            selectedInGroup(groupClass) {
                let count = 0;
                document.querySelectorAll(`.${groupClass}`).forEach(cb => {
                    if (this.currentState[cb.id]) count++;
                });
                return count;
            },
            
            isGroupFullySelected(groupClass) {
                const checkboxes = document.querySelectorAll(`.${groupClass}`);
                if (checkboxes.length === 0) return false;
                
                for (let cb of checkboxes) {
                    if (!this.currentState[cb.id]) return false;
                }
                return true;
            },
            
            toggleGroup(groupClass, checked) {
                document.querySelectorAll(`.${groupClass}`).forEach(cb => {
                    cb.checked = checked;
                    this.currentState[cb.id] = checked;
                });
            },
            
            selectAll() {
                document.querySelectorAll('.perm-checkbox').forEach(cb => {
                    cb.checked = true;
                    this.currentState[cb.id] = true;
                });
            },
            
            deselectAll() {
                document.querySelectorAll('.perm-checkbox').forEach(cb => {
                    cb.checked = false;
                    this.currentState[cb.id] = false;
                });
            },
            
            reset() {
                document.querySelectorAll('.perm-checkbox').forEach(cb => {
                    cb.checked = this.initialState[cb.id];
                    this.currentState[cb.id] = this.initialState[cb.id];
                });
            }
        }))
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\admin\roles\assignpermission.blade.php ENDPATH**/ ?>